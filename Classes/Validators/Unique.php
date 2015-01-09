<?php

namespace Ameos\AmeosForm\Validators;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Unique extends \Ameos\AmeosForm\Validators\ValidatorAbstract {

	/**
	 * return true if the element is sameas an another value
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		if($value == ''){
			return true;
		}
		$objectManager = GeneralUtility::makeInstance('\\TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$defaultQuerySettings = $objectManager->get('\\TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$defaultQuerySettings->setRespectStoragePage(FALSE);
		$this->configuration['repository']->setDefaultQuerySettings($defaultQuerySettings);
		
		$field   = $this->element->getName();
		$method  = 'findBy' . ucfirst($field);
		$records = $this->configuration['repository']->$method($value);
		$isValid = TRUE;
		foreach($records as $record) {
			if($record->getUid() != $this->form->getModel()->getUid()) {
				$isValid = FALSE;
			}
		}
		return $isValid;
	}
}
