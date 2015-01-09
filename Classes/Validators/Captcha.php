<?php

namespace Ameos\AmeosForm\Validators;

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Captcha extends \Ameos\AmeosForm\Validators\ValidatorAbstract {
	
	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value) {
		require_once(ExtensionManagementUtility::extPath('ameos_form') . 'Classes/Contrib/SecureImage/securimage.php');
		$securimage = new \Securimage();
		return $securimage->check($value);
	}
}
