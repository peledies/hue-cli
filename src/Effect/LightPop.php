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
          ->setName('effect:lightpop')
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
        )->getRGB();

        $light = new \Phue\Command\SetLightState($input->getOption('id'));

        $client->sendCommand(
          $light
            ->rgb(
                  $input->getOption('red')
                , $input->getOption('green')
                , $input->getOption('blue')
              )
            ->transitionTime(0)
        );
        
        usleep(300000);
        
        $client->sendCommand(
          $light
            ->rgb(
                  $original['red']
                , $original['green']
                , $original['blue']
              )
            ->transitionTime(2)
        );
    
    }


}

