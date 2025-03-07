<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Exception\MissingDependencyException;
use ReCaptcha\ReCaptcha as GoogleReCaptcha;

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
        if (!isset($_POST['g-recaptcha-response'])) {
            return false;
        }

        if (!class_exists(GoogleReCaptcha::class)) {
            throw new MissingDependencyException('install recaptcha library (composer require google/recaptcha)');
        }

        $recaptcha = new GoogleReCaptcha($this->configuration['privateKey']);
        $response  = $recaptcha->verify($_POST['g-recaptcha-response']);

        if ($response->isSuccess()) {
            return true;
        }
        return false;
    }
}
