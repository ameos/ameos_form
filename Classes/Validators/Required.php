<?php

namespace Ameos\AmeosForm\Validators;

class Required extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		return !(trim($value) == '');
	}
}
