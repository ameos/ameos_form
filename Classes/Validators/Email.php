<?php

namespace Ameos\AmeosForm\Validators;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Email extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		if($value == '') return TRUE;
		return GeneralUtility::validEmail($value);
	}
}
