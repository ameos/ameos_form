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
		if(is_a($this->element, '\\Ameos\\AmeosForm\\Elements\\Upload')) {
			if(is_array($value) && array_key_exists('upload', $value) && is_array($value['upload'])) {
				return TRUE;
			}
		}
		return !(trim($value) == '');
	}
}
