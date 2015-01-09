<?php

namespace Ameos\AmeosForm\Validators;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Fileextension extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		if(!is_array($value) && empty($value)) {
			return TRUE;
		}

		if(!is_array($value) || !array_key_exists('upload', $value) || !is_array($value['upload'])) {
			return TRUE;
		}
		
		if(is_array($value['upload'])) {
			$pathfinfo = pathinfo($value['upload']['name']);
			return GeneralUtility::inList($this->configuration['allowed'], strtolower($pathfinfo['extension']));	
		}

		return FALSE;
	}
}
