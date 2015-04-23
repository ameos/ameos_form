<?php
namespace Ameos\AmeosForm\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
