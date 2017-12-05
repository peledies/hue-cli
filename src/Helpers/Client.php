<?php
namespace Hue\Helpers;

require_once 'vendor/autoload.php';

class Client extends \Phue\Client
{
	
	public function __construct($name = null){
		$bridge = new \Hue\Helpers\Bridge();

        if(is_null($name) && empty(config('user'))){
        	throw new \Exception("No user defined in .env file", 1);
        }

		$user = (!is_null($name))? $name : config('user');
        return parent::__construct($bridge->getIp(), $user);
	}
}