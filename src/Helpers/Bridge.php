<?php
namespace Hue\Helpers;

require_once 'vendor/autoload.php';

class Bridge
{
	protected $ip;

	protected $id;

	public function __construct(){
		$response = @file_get_contents('http://www.meethue.com/api/nupnp');

        // Don't continue if bad response
        if ($response === false) {
        	throw new \Exception("Request failed. Ensure that you have internet connection, and that the bridge is properly connected", 1);
        }

        // Parse the JSON response
        $bridges = json_decode($response);
        $bridge = array_shift($bridges);
        $this->ip = $bridge->internalipaddress;
        $this->id = $bridge->id;
	}

	public function getIp(){
		return $this->ip;
	}

	public function getId(){
		return $this->id;
	}

	public function getMac(){
		return $this->bridge->macaddress;
	}

	public function all(){
		return $this;
	}


}