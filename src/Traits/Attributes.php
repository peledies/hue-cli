<?php
namespace Hue\Traits;

trait Attributes {

	public function getAttributes($obj){
		$reflection = new \ReflectionClass($obj);
		$property = $reflection->getProperty('attributes');
		$property->setAccessible(true);
		return $property->getValue($obj);
	}
}