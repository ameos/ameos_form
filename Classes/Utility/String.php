<?php

namespace Ameos\AmeosForm\Utility;

class String {

	/**
	 * camel case
	 * @param string $string
	 * @return string
     */
	public static function camelCase($string) {
		$output = '';
		foreach( explode('_', $string) as $part) {
			$output.= ucfirst($part);
		}
		return $output;
	}

	/**
	 * Replace EXT, EXTCONF ...
	 * 
	 * @param string $string the raw string
	 * @return string eval string
	 */ 
	public static function smart($string) {
// TODO
		return $string;
		
	}
}
