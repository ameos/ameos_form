<?php

namespace Ameos\AmeosForm\Validators;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Custom extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		if(!isset($this->configuration['method']) && !is_callable($this->configuration['method'])) {
			throw new \Exception('Custom validator must have a callable method for validation');
		}
		return $this->configuration['method']($value, $this->form);
	}
}
