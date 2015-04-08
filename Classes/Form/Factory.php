<?php

namespace Ameos\AmeosForm\Form;

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
			return GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Form\\Search\\ExtbaseForm', $arguments[0], $arguments[1]);
		}

		if(is_string($arguments[0]) && is_a($arguments[1], '\\TYPO3\\CMS\\Extbase\\Persistence\\Repository')) {
			throw new \Exception('Your repository must extends \\Ameos\\AmeosForm\\Domain\\Repository\\SearchableRepository');
		}

		if(is_string($arguments[0]) && is_a($arguments[1], '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {
			return GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Form\\Crud\\ExtbaseForm', $arguments[0], $arguments[1]);
		}

		if(is_string($arguments[0]) && is_string($arguments[1])) {
			if(isset($arguments[2])) {
				return GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Form\\Crud\\ClassicForm', $arguments[0], $arguments[1], (int)$arguments[2]);
			} else {
				return GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Form\\Crud\\ClassicForm', $arguments[0], $arguments[1]);
			}
		}

		if(sizeof($arguments) == 1) {
			return GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Form\\Crud', $arguments[0]);
		}

		throw new \Exception('Impossible to make the form with these arguments');
	}
	
}
