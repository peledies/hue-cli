<?php
namespace Hue\Light;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class Update extends Command
{
    use \Hue\Traits\Attributes;

    protected function configure()
    {
        $this
          ->addOption('id','i',InputOption::VALUE_REQUIRED,'Bulb ID')
          ->addOption('red','r',InputOption::VALUE_REQUIRED,'Red value (0 - 255)')
          ->addOption('green','g',InputOption::VALUE_REQUIRED,'Green value (0 - 255)')
          ->addOption('blue','b',InputOption::VALUE_REQUIRED,'Blue value (0 - 255)')
          ->setName('light:update')
          ->setDescription('Command for updating light color')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Hue\Helpers\Client();

        $light = new \Phue\Command\SetLightState($input->getOption('id'));

        $xy = \Phue\Helper\ColorConversion::convertRGBToXY(
            $input->getOption('red')
          , $input->getOption('green')
          , $input->getOption('blue')
        );

        $command = $light
            ->brightness(255)
            ->hue(0)
            ->saturation(255)
            ->colorTemp(153)
            ->xy($xy['x'], $xy['y'])
            ->transitionTime(1);

        $client->sendCommand($command);
    
    }


}

