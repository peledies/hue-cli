<?php
namespace Hue\User;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

class Delete extends Command
{

    protected function configure()
    {
        $this
          ->addArgument('name', InputArgument::REQUIRED)
          ->setName('user:delete')
          ->setDescription('Command for Deleting a user')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $client = new \Hue\Helpers\Client();
        
        $users = $client->sendCommand(
            new \Phue\Command\GetUsers()
        );

        foreach($users as $user){
          if ($user->getUsername() == $name) {
              $user->delete();
          }
        };

    }

}
