<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

class Loop extends Command
{

    protected function configure()
    {
        $this
          ->setName('effect:loop')
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
        
    		$x = new \Phue\Command\SetGroupState(0);
        //$y = $x->effect('none');
    		$y = $x->rgb(255, 0, 0);
    		
    		$client->sendCommand($y); 

    }

}