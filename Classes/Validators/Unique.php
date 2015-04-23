<?php
namespace Ameos\AmeosForm\Validators;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$defaultQuerySettings = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
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
