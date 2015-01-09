<?php

namespace Ameos\AmeosForm\Validators;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Sameas extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

	/**
	 * return true if the element is sameas an another value
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		return $value == $this->form->getElement($this->configuration['sameas'])->getValue();
	}
}
