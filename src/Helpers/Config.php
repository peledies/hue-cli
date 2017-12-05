<?php
namespace Hue\Helpers;

require_once 'vendor/autoload.php';

class Config
{
	
	public function __construct(){
		foreach (parse_ini_file('.env') as $key => $value) {
			$this->$key = $value;
		}
	}

	public function get($property){
  		if(isset($this->$property)){
  			return $this->$property;
  		}
	}
}