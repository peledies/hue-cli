<?php
namespace Hue\Traits;

use Symfony\Component\Console\Input\InputInterface;

trait Validate {

	public function validate(InputInterface $input, Array $validate = []){
		$throw = false;
		$msg = [];

		$lookup = [
			  'i' => 'id'
			, 'm' => 'name'
			, 'r' => 'red'
			, 'g' => 'green'
			, 'b' => 'blue'
		];

		$cast = [
			  'i' => 'int'
			, 'm' => 'string'
			, 'r' => 'int'
			, 'g' => 'int'
			, 'b' => 'int'
		];

		foreach ($validate as $option) {
			if(array_key_exists($option, $lookup)){
				$this->{$lookup[$option]} = ($cast[$option] == 'int')
					? (int) $input->getOption($lookup[$option])
					: $input->getOption($lookup[$option]);
			}
		}

		if(in_array('r', $validate) && is_null($this->red)){
			$throw = true;
			$msg[] = "\n\nYou must specify -r [red] (0 - 255)";
		}

		if(in_array('g', $validate) && is_null($this->green)){
			$throw = true;
			$msg[] = "\n\nYou must specify -g [green] (0 - 255)";
		}

		if(in_array('b', $validate) && is_null($this->blue)){
			$throw = true;
			$msg[] = "\n\nYou must specify -b [blue] (0 - 255)";
		}

		if( 
			( in_array('i', $validate) && !isset($this->id) )
			 && 
			( in_array('m', $validate) && !isset($this->name) )
		){
			$throw = true;
			$msg[] = "\n\nYou must specify either -m [name] or -i [id] of a bulb";
		}

		if($throw){
			throw new \Exception(implode('', $msg), 1);
		}
	}

	public function nameToIdOrId(\Hue\Helpers\Client $client){
		 if(!is_null($this->name)){
			$light = array_filter($client->getLights(), function($light) {
				return (strtolower($light->getName()) == strtolower($this->name));
			});

			if($light){
				$this->id = (int) array_shift($light)->getId();
			}else{
				throw new \Exception("Bulb with name [ {$this->name} ] could not be found");
			}
        }
        
        return (int) $this->id;
	}
}