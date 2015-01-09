<?php

namespace Ameos\AmeosForm\Validators;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Filesize extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

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
			$maxsize = (int)$this->configuration['maxsize'];
			if(strtoupper(substr($this->configuration['maxsize'], -1)) == 'K' || strtoupper(substr($this->configuration['maxsize'], -1)) == 'KO') {
				$maxsize = $maxsize * 1024;
			}
			if(strtoupper(substr($this->configuration['maxsize'], -1)) == 'M' || strtoupper(substr($this->configuration['maxsize'], -1)) == 'MO') {
				$maxsize = $maxsize * 1024 * 1024;
			}
			if(strtoupper(substr($this->configuration['maxsize'], -1)) == 'G' || strtoupper(substr($this->configuration['maxsize'], -1)) == 'GO') {
				$maxsize = $maxsize * 1024 * 1024 * 1024;
			}

			return (int)$value['upload']['size'] <= $maxsize;
		}
		return FALSE;
	}
}
