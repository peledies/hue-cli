<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class LightPop extends Command
{
    use \Hue\Traits\Attributes;

    protected function configure()
    {
        $this
          ->addOption('id','i',InputOption::VALUE_REQUIRED,'Bulb ID')
          ->addOption('red','r',InputOption::VALUE_REQUIRED,'Red value (0 - 255)')
          ->addOption('green','g',InputOption::VALUE_REQUIRED,'Green value (0 - 255)')
          ->addOption('blue','b',InputOption::VALUE_REQUIRED,'Blue value (0 - 255)')
          ->setName('effect:light-pop')
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
            new \Phue\Command\GetLightById($input->getOption('id'))
        );

        if(property_exists($this->getAttributes($original)->state, 'colormode')){
          $og = [
              'id'=> $input->getOption('id')
            , 'bri' => $original->getBrightness()
            , 'hue' => $original->getHue()
            , 'sat' => $original->getSaturation()
            , 'ct' => $original->getColorTemp()
          ];

          $og = array_merge($og, $original->getXY());
        }else{
          $og = [
              'id'=>$input->getOption('id')
            , 'bri'=>$original->getBrightness()
          ];
        }

        $light = new \Phue\Command\SetLightState($input->getOption('id'));

        $xy = \Phue\Helper\ColorConversion::convertRGBToXY(
            $input->getOption('red')
          , $input->getOption('green')
          , $input->getOption('blue')
        );

        $client->sendCommand(
          $light
            ->hue(0)
            ->saturation(255)
            ->colorTemp(153)
            ->xy($xy['x'], $xy['y'])
            ->transitionTime(0)
        );
        
        usleep(300000);
        
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
