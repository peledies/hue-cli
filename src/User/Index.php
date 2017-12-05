<?php
namespace Hue\User;

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
          ->addArgument('output', InputArgument::OPTIONAL, 'table|json (default - json)')
          ->setName('user:index')
          ->setDescription('Command for listing bridge users')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $outputFormat = (empty($input->getArgument('output')))
        ? 'json'
        : $input->getArgument('output');

        $bridge = new \Hue\Helpers\Bridge();
        
        $client = new \Phue\Client($bridge->getIp(), config('user'));
        
        $response = $client->sendCommand(
            new \Phue\Command\GetUsers()
        );

        $data = array_map(function($item){
          $attributes = $this->getAttributes($item);
          return [
              'username' => $item->getUsername()
            , 'user' => $attributes->name
            , 'lastUsed' => $item->getLastUseDate()
            , 'created' => $item->getCreateDate()
          ];
        }, $response);

        $table = new Table($output);
        $table
            ->setHeaders(array('Username', 'user', 'Last Login', 'Created'))
            ->setRows($data);
        $table->render();

    }

}

