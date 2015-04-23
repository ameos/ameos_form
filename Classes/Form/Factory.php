<?php
namespace Ameos\AmeosForm\Form;

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

class Factory {

	/**
	 * create and return FORM
	 * @return AbstractForm the form
	 * @throws \Exception
	 */
	public static function make() {
		$arguments = func_get_args();
		if(is_string($arguments[0]) && is_a($arguments[1], '\\Ameos\\AmeosForm\\Domain\\Repository\\SearchableRepository')) {
			return GeneralUtility::makeInstance('Ameos\\AmeosForm\\Form\\Search\\ExtbaseForm', $arguments[0], $arguments[1]);
		}

		if(is_string($arguments[0]) && is_a($arguments[1], '\\TYPO3\\CMS\\Extbase\\Persistence\\Repository')) {
			throw new \Exception('Your repository must extends \\Ameos\\AmeosForm\\Domain\\Repository\\SearchableRepository');
		}

		if(is_string($arguments[0]) && is_a($arguments[1], '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {
			return GeneralUtility::makeInstance('Ameos\\AmeosForm\\Form\\Crud\\ExtbaseForm', $arguments[0], $arguments[1]);
		}

		if(is_string($arguments[0]) && is_string($arguments[1])) {
			if(isset($arguments[2])) {
				return GeneralUtility::makeInstance('Ameos\\AmeosForm\\Form\\Crud\\ClassicForm', $arguments[0], $arguments[1], (int)$arguments[2]);
			} else {
				return GeneralUtility::makeInstance('Ameos\\AmeosForm\\Form\\Crud\\ClassicForm', $arguments[0], $arguments[1]);
			}
		}

		if(sizeof($arguments) == 1) {
			return GeneralUtility::makeInstance('Ameos\\AmeosForm\\Form\\Crud', $arguments[0]);
		}

		throw new \Exception('Impossible to make the form with these arguments');
	}
	
}
