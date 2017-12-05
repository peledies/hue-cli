<?php
namespace Hue\Traits;

trait Sort {

	private function sortByKey($collection,$key)
    {
      usort($collection, function($a,$b) use ($key){
        if ($a[$key] == $b[$key]) return 0;
        return ($a[$key] > $b[$key]) ? 1 : -1;
      });
      return $collection;
    }
}