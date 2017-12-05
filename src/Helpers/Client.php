<?php
namespace Hue\Helpers;

require_once 'vendor/autoload.php';

class Client extends \Phue\Client
{
	
	public function __construct(){
		$bridge = new \Hue\Helpers\Bridge();

        if(empty(config('user'))){
        	throw new \Exception("No user defined in .env file", 1);
        }
        return parent::__construct($bridge->getIp(), config('user'));
	}
}