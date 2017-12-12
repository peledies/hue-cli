<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class DiagonalWave extends Command
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
          ->setName('effect:diagonal-wave')
          ->setDescription('a diagonal wave')
          ->addOption('transition','t',InputOption::VALUE_REQUIRED,'Transition Speed (seconds)')
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

      $lights = array_map(function($light){
          return [
              'id' => $light->getId()
            , 'name' => $light->getName()
          ];
        }, $client->getLights());
      
      $groups = [];
      for ($i = 'A'; $i !== 'Z'; $i++){
        $groups[] = array_filter($lights, function($light) use ($i){
          return (preg_match("/$i-/",$light['name']));
        });
      }

      foreach ($groups as $key => $group) {
        $groups[$key] = $this->sortByKey($group, 'name');
        if(empty($groups[$key])){
            unset($groups[$key]);
          }
      }

      $size = [count($groups), count($groups[0])];

      $dig = [];
      for ($i=0; $i < $size[0] + $size[1] - 1; $i++) { 
        // groups to shift
        for ($ii=0; $ii < $i + 1; $ii++) { 
          if(!empty($groups[$ii])){
            $dig[$i][] = array_shift($groups[$ii]);
          }
        }
      }

      $wave = 0;
      while(true){

        $xy = \Phue\Helper\ColorConversion::convertRGBToXY(
            $this->red
          , $this->green
          , $this->blue
        );

        foreach ($dig as $key => $group) {
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
        $this->red = rand(0, 255);
        $this->green = rand(0, 255);
        $this->blue = rand(0, 255);

        dump("Wave - $wave");
        dump("R: $this->red, G: $this->green B: $this->blue");
      }    

    }

}
