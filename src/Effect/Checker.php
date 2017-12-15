<?php
namespace Hue\Effect;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

class Checker extends Command
{
    use \Hue\Traits\Sort;

    // starting value for red
    protected $red = 0;

    // starting value for green
    protected $green = 125;

    // starting value for blue
    protected $blue = 125;

    protected function configure()
    {
        $this
          ->setName('effect:checker')
          ->setDescription('checker board pattern')
          ->addOption('transition','t',InputOption::VALUE_REQUIRED,'Transition Speed (seconds)')
          ->addOption('christmas','c',InputOption::VALUE_NONE,'Christmas Mode')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

      $this->client = new \Hue\Helpers\Client();

      $this->transition = (is_null($input->getOption('transition')))? 3 : (int) $input->getOption('transition');
      $this->christmas = $input->getOption('christmas');
      $this->christmas_phase = $this->christmas;

      $lights = array_map(function($light){
          return [
              'id' => $light->getId()
            , 'name' => $light->getName()
          ];
        }, $this->client->getLights());

      $groups = [];
      for ($i = 'A'; $i !== 'Z'; $i++){
        $groups[] = array_filter($lights, function($light) use ($i){
          return (preg_match("/$i-/",$light['name']));
        });
      }

      foreach ($groups as $key => $group) {
        $groups[$key] = $this->sortByKey($group, 'name');
        if(empty($groups[$key])){
            unset($groups[$key]);
          }
      }

      $even = [];
      $odd = [];

      foreach ($groups as $key => $group) {
          if($key & 1){
            $odd = array_merge(array_filter($group, function($v,$k){
                return(!($k & 1));
            }, ARRAY_FILTER_USE_BOTH), $odd);
            $even = array_merge(array_filter($group, function($v,$k){
                return($k & 1);
            }, ARRAY_FILTER_USE_BOTH), $even);
          }else{
            $odd = array_merge(array_filter($group, function($v,$k){
                return($k & 1);
            }, ARRAY_FILTER_USE_BOTH), $odd);
             $even = array_merge(array_filter($group, function($v,$k){
                return(!($k & 1));
            }, ARRAY_FILTER_USE_BOTH), $even);
          }
      }

      $even = $this->sortByKey($even, 'name');
      $odd = $this->sortByKey($odd, 'name');

      $wave = 0;
      while(true){
        $this->checker($odd);
        
        if(!$this->christmas){
          sleep($this->transition * 2);
        }

        $this->christmas_phase = !$this->christmas_phase;
        $this->checker($even);


        sleep($this->transition * 2);
        $wave++;
        dump("Wave - $wave");

      }
    }

    private function checker(Array $lights){
        
        $xy = ($this->christmas)
          ? \Phue\Helper\ColorConversion::convertRGBToXY(
                ($this->christmas_phase)? 255 : 0
              , ($this->christmas_phase)? 0 : 255
              , 0
            )
          : \Phue\Helper\ColorConversion::convertRGBToXY(
                rand(0, 255)
              , rand(0, 255)
              , rand(0, 255)
            );

        foreach ($lights as $bulb) {
          $light = new \Phue\Command\SetLightState($bulb['id']);

          $command = $light
              ->hue(0)
              ->saturation(255)
              ->colorTemp(153)
              ->xy($xy['x'], $xy['y'])
              ->transitionTime($this->transition);

          $this->client->sendCommand($command);
        }

    }

}