<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

class Wave extends Command
{

    protected function configure()
    {
        $this
          ->setName('effect:wave')
          ->setDescription('front to back wave')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        

        $client = new \Hue\Helpers\Client();
        
        $light = $client->sendCommand(
            new \Phue\Command\GetLightById($id)
        );

        
            dump($light);
            $light->delete();
         

    }

}
