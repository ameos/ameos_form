<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ReCaptcha extends ConstraintAbstract
{
    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        require_once ExtensionManagementUtility::extPath('ameos_form') . 'Classes/Contrib/ReCaptcha/autoload.php';

        if (!isset($_POST['g-recaptcha-response'])) {
            return false;
        }

        $recaptcha = new \ReCaptcha\ReCaptcha($this->configuration['privateKey']);
        $response  = $recaptcha->verify($_POST['g-recaptcha-response']);

        if ($response->isSuccess()) {
            return true;
        }
        return false;
    }
}
