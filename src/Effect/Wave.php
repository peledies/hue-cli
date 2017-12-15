<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class Wave extends Command
{
    use \Hue\Traits\Sort;

    // starting value for red
    protected $red = 0;

    // starting value for green
    protected $green = 125;

    // starting value for blue
    protected $blue = 125;

    protected function configure()
    {
        $this
          ->setName('effect:wave')
          ->addOption('transition','t',InputOption::VALUE_REQUIRED,'Transition Speed (seconds)')
          ->setDescription('front to back wave')
          ->addOption('christmas','c',InputOption::VALUE_NONE,'Christmas Mode')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Hue\Helpers\Client();

        $transition = (is_null($input->getOption('transition')))? 3 : (int) $input->getOption('transition');
        $this->christmas = $input->getOption('christmas');
        $this->christmas_phase = $this->christmas;

        $lights = array_map(function($light){
          return [
              'id' => $light->getId()
            , 'name' => $light->getName()
          ];
        }, $client->getLights());
        
        $groups = [];
        // group into row arrays
        for ($i = 'A'; $i !== 'Z'; $i++){
          $groups[] = array_filter($lights, function($light) use ($i){
            return (preg_match("/$i/",$light['name']));
          });
        }

        // order the rows by matrix order
        foreach ($groups as $key => $group) {
          $groups[$key] = $this->sortByKey($group, 'name');
          if(empty($groups[$key])){
            unset($groups[$key]);
          }
        }

        $wave = 0;
        while(true){

          $this->christmas_phase = !$this->christmas_phase;

          $xy = ($this->christmas)
            ? \Phue\Helper\ColorConversion::convertRGBToXY(
                  ($this->christmas_phase)? 255 : 0
                , ($this->christmas_phase)? 0 : 255
                , 0
              )
            : \Phue\Helper\ColorConversion::convertRGBToXY(
                  rand(0, 255)
                , rand(0, 255)
                , rand(0, 255)
              );

          foreach ($groups as $key => $group) {

            foreach ($group as $bulb) {
             
              $light = new \Phue\Command\SetLightState($bulb['id']);

              $command = $light
                  ->hue(0)
                  ->saturation(255)
                  ->colorTemp(153)
                  ->xy($xy['x'], $xy['y'])
                  ->transitionTime($transition);

              $client->sendCommand($command);

            }
            sleep($transition);
          }

          $wave++;

          dump("Wave - $wave");
        }
         

    }

}
