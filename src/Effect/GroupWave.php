<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class GroupWave extends Command
{
    use \Hue\Traits\Sort;
    use \Hue\Traits\Validate;
    use \Hue\Traits\Attributes;

    // starting value for red
    protected $red = 0;

    // starting value for green
    protected $green = 125;

    // starting value for blue
    protected $blue = 125;

    protected function configure()
    {
        $this
          ->setName('effect:group-wave')
          ->addOption('id','i',InputOption::VALUE_REQUIRED,'Group ID')
          ->addOption('name','m',InputOption::VALUE_REQUIRED,'Group Name')
          ->addOption('transition','t',InputOption::VALUE_REQUIRED,'Transition Speed (seconds)')
          ->setDescription('front to back wave for a group')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Hue\Helpers\Client();
        
        $this->validate($input, ['i','m']);
        $this->nameToIdOrId($client);

        $transition = (is_null($input->getOption('transition')))? 3 : (int) $input->getOption('transition');

        $light_ids = array_shift(array_map(function($group){
          return $this->getAttributes($group)->lights;
        }, $client->getGroups()));
        
        $light_obs = array_filter($client->getLights(), function($light) use ($light_ids){
          return (in_array($light->getId(), $light_ids));
        });

        $lights = array_map(function($light){
          return [
              'id' => $light->getId()
            , 'name' => $light->getName()
          ];
        }, $light_obs);

        $groups = [];
        // group into row arrays
        for ($i = 'A'; $i !== 'Z'; $i++){
          $groups[] = array_filter($lights, function($light) use ($i){
            return (preg_match("/$i-/",$light['name']));
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

          $xy = \Phue\Helper\ColorConversion::convertRGBToXY(
              $this->red
            , $this->green
            , $this->blue
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
          $this->red = rand(0, 255);
          $this->green = rand(0, 255);
          $this->blue = rand(0, 255);

          dump("Wave - $wave");
          dump("R: $this->red, G: $this->green B: $this->blue");
        }
         

    }

}
