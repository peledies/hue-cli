<?php
namespace Hue\Light;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableCell;

class Index extends Command
{
    use \Hue\Traits\Attributes;

    protected function configure()
    {
        $this
          ->setName('light:index')
          ->setDescription('Command for listing Philips Hue Bulbs')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //$bridge = new \Hue\Helpers\Bridge();
        $client = new \Hue\Helpers\Client();
        
        $lights = array_map(function($light){
          $a = $this->getAttributes($light);
          return [
              'id' => $light->getId()
            , 'name' => $a->name
            , 'state' => ($a->state->reachable)
                    ? ($a->state->on)
                      ? 'On'
                      : 'Off'
                    : null
            , 'reachable' => ($a->state->reachable)? 'Yes':'No'
            , 'level' => ($a->state->reachable)
                    ? $a->state->bri
                    : null];
        }, $client->getLights());

        $table = new Table($output);
        $table
            ->setHeaders([
                'ID'
              , 'Name'
              , 'State'
              , 'Reachable'
              , 'Level'
            ])
            ->setRows($lights);
        $table->render();


    }


}

