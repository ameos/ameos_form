<?php

namespace Ameos\AmeosForm\Validators;

interface ValidatorInterface {

	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value);

	/**
	 * return the message
	 * 
	 * @return	string the message
	 */
	public function getMessage();
}
