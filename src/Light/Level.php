<?php
namespace Hue\Light;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableCell;

class Level extends Command
{
    use \Hue\Traits\Attributes;

    protected function configure()
    {
        $this
          ->addOption(
              'brightness',
              'b',
              InputOption::VALUE_REQUIRED,
              'Brightness level'
          )
          ->addOption(
              'light',
              'l',
              InputOption::VALUE_REQUIRED,
              'ID of the light to update'
          )
          ->setName('light:level')
          ->setDescription('Command for updating light brightness')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Hue\Helpers\Client();

        $state = new \Phue\Command\SetLightState($input->getOption('light'));
        $command = $state->brightness($input->getOption('brightness'))
            ->colorTemp(450)
            ->transitionTime(1);
        $client->sendCommand($command);
    
    // Sleep for transition time plus extra for request time
    //usleep($transitionTime * 1000000 + 25000);
//}
    }


}

