<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Elements\Numericcaptcha as NumericcaptchaElement;

class Numericcaptcha extends ConstraintAbstract
{
    /**
     * @var NumericcaptchaElement $element element
     */
    protected $element;

    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        if (!(intval($value) === $this->element->getDigit(1) + $this->element->getDigit(2))) {
            $this->element->reloadDigit(1);
            $this->element->reloadDigit(2);
            return false;
        } else {
            return true;
        }
    }
}
