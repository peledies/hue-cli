<?php
namespace Hue\User;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;
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
          ->setName('user:index')
          ->addOption(
              'sort',
              's',
              InputOption::VALUE_REQUIRED,
              'Sort by column name'
          )
          ->addArgument('output', InputArgument::OPTIONAL, 'table|json (default - json)')
          ->setDescription('Command for listing bridge users')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Hue\Helpers\Client();
        
        $response = $client->sendCommand(
            new \Phue\Command\GetUsers()
        );

        $data = array_map(function($item){
          $attributes = $this->getAttributes($item);
          return [
              'username' => $item->getUsername()
            , 'user' => $attributes->name
            , 'lastLogin' => $item->getLastUseDate()
            , 'created' => $item->getCreateDate()
          ];
        }, $response);

        $sort = ($input->getOption('sort'))? $input->getOption('sort') : 'created';
        $data = $this->sortByKey($data, $sort);

        $table = new Table($output);
        $table
            ->setHeaders(array('Username', 'user', 'Last Login', 'Created'))
            ->setRows($data);
        $table->render();

    }

}

