<?php

namespace Ameos\AmeosForm\Elements;

interface ElementInterface {

	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml();

	/**
	 * return true if the element is valide
	 *
	 * @return	bool true if the element is valide
	 */
	public function isValid();

	/**
	 * add configuration
	 *
	 * @param	string	$key configuration key
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function addConfiguration($key, $value);
}
