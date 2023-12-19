<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Captcha extends ConstraintAbstract
{
    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        require_once ExtensionManagementUtility::extPath('ameos_form') . 'Classes/Contrib/SecureImage/securimage.php';
        $securimage = new \Securimage();
        return $securimage->check($value);
    }
}
