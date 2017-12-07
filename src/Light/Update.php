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
    use \Hue\Traits\Validate;

    protected function configure()
    {
        $this
          ->addOption('id','i',InputOption::VALUE_REQUIRED,'Bulb ID')
          ->addOption('name','m',InputOption::VALUE_REQUIRED,'Bulb Name')
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
        
        $this->validate($input, ['i','m','r','g','b']);

        $this->nameToIdOrId($client);

        $light = new \Phue\Command\SetLightState($this->id);

        $xy = \Phue\Helper\ColorConversion::convertRGBToXY(
            $this->red
          , $this->green
          , $this->blue
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

