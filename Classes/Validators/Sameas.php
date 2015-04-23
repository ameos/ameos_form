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
