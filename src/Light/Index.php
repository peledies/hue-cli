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

class Index extends Command
{
    use \Hue\Traits\Attributes;
    use \Hue\Traits\Sort;

    protected function configure()
    {
        $this
          ->setName('light:index')
          ->addOption(
              'sort',
              's',
              InputOption::VALUE_REQUIRED,
              'Sort by column name'
          )
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

        $sort = ($input->getOption('sort'))? $input->getOption('sort') : 'id';
        $lights = $this->sortByKey($lights, $sort);

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

