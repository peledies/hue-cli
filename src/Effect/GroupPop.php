<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class GroupPop extends Command
{
    use \Hue\Traits\Attributes;

    protected function configure()
    {
        $this
          ->addOption('id','i',InputOption::VALUE_REQUIRED,'Group ID')
          ->addOption('red','r',InputOption::VALUE_REQUIRED,'Red value (0 - 255)')
          ->addOption('green','g',InputOption::VALUE_REQUIRED,'Green value (0 - 255)')
          ->addOption('blue','b',InputOption::VALUE_REQUIRED,'Blue value (0 - 255)')
          ->setName('effect:grouppop')
          ->setDescription('flash a color then return')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Hue\Helpers\Client();

        $original = $client->sendCommand(
            new \Phue\Command\GetGroupById($input->getOption('id'))
        );
        
        $groupAttributes = $this->getAttributes($original);

        $ogLights = array_map(function($id) use ($client, $input){
          
          $light = $client->sendCommand(
              new \Phue\Command\GetLightById($id)
            );

          if(property_exists($this->getAttributes($light)->state, 'colormode')){
            $color = [
                'id'=>$id
              , 'bri' => $light->getBrightness()
              , 'hue' => $light->getHue()
              , 'sat' => $light->getSaturation()
              , 'ct' => $light->getColorTemp()
            ];

            $color = array_merge($color, $light->getXY());
          }else{
            $color = [
                'id'=>$id
              , 'bri'=>$light->getBrightness()
            ];
          }
          
          return $color;
        }, $groupAttributes->lights);

        $group = new \Phue\Command\SetGroupState($input->getOption('id'));

        $xy = \Phue\Helper\ColorConversion::convertRGBToXY(
            $input->getOption('red')
          , $input->getOption('green')
          , $input->getOption('blue')
        );

        $client->sendCommand(
          $group
            ->hue(0)
            ->saturation(255)
            ->colorTemp(153)
            ->xy($xy['x'], $xy['y'])
        );

        usleep(300000);
        
        foreach ($ogLights as $og) {
          $light = new \Phue\Command\SetLightState($og['id']);
          if(array_key_exists('hue', $og)){
            $command = $light
                ->hue($og['hue'])
                ->saturation($og['sat'])
                ->colorTemp($og['ct'])
                ->xy($og['x'], $og['y'])
                ->transitionTime(2);
          }else{
            $command = $light
                ->brightness( (int) $og['bri'] )
                ->transitionTime(2);
          }
          $client->sendCommand($command);
        }
        
    
    }


}

