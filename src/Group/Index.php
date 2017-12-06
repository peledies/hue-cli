<?php
namespace Hue\Group;

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
          ->setName('group:index')
          ->setDescription('Command for listing light groups')
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
        
        $groups = array_map(function($group){
          $a = $this->getAttributes($group);
          return [
              'id' => $group->getId()
            , 'name' => $a->name
            , 'all_on' => ($a->state->all_on)? "Yes":"No"
            , 'any_on' => ($a->state->any_on)? "Yes":"No"
          ];
        }, $client->getGroups());

        $table = new Table($output);
        $table
            ->setHeaders([
                'ID'
              , 'Name'
              , 'All On'
              , 'Any On'
            ])
            ->setRows($groups);
        $table->render();


    }


}

