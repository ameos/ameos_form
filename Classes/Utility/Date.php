<?php

namespace Ameos\AmeosForm\Utility;

class Date {

	/**
	 * convert date to timestamp
	 * @param string $value
	 * @param string $format
	 * @return string
     */
	public static function dateToTimestamp($value, $format) {
		if($value == '') {
			return 0;
		}
		$format = str_replace('%', '', $format);
		$date = \Datetime::createFromFormat($format, $value);
		return $date->getTimestamp();
	}

	/**
	 * convert timestamp to date
	 * @param string $value
	 * @param string $format
	 * @return string
     */
	public static function timestampToDate($value, $format) {
		if(strpos($format, '%')) {
			return strftime($format, $value);
		}
		return date($format, $value);
	}
}
