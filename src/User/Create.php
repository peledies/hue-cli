<?php
namespace Hue\User;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

class Create extends Command
{

    protected function configure()
    {
        $this
          ->addArgument('name', InputArgument::REQUIRED)
          ->setName('user:create')
          ->setDescription('Command for creating a user')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $client = new \Hue\Helpers\Client($name);
        
        if(empty(config('user'))){

            $maxTries = 30;
            echo "\nYou have $maxTries seconds to press the button on the bridge.\n";
            for ($i = 1; $i <= $maxTries; ++$i) {
                try {
                    $user = $client->sendCommand(
                        new \Phue\Command\CreateUser($name)
                    );
                    
                    echo "\nUsername:\n  $user->username\n";
                    break;
                } catch (\Phue\Transport\Exception\LinkButtonException $e) {
                    echo ".";
                } catch (Exception $e) {
                    echo "\n\n", "Failure to create user. Please try again!",
                         "\n", "Reason: {$e->getMessage()}", "\n\n";
                    break;
                }
                sleep(1);
            }
            if(!empty($user)){
                $this->writeToConfig("\nUSER=$user->username");
            }
        }else{
            echo "\nUser: ".config('user')." already exists\n";
        }

    }

    private function writeToConfig($line){
        $file = fopen(".env", "a") or die("Unable to open file!");
        fwrite($file, $line);
        fclose($file);
    }

}
