<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Elements\Numericcaptcha as NumericcaptchaElement;
use Ameos\AmeosForm\Exception\BadConfigurationException;

class Numericcaptcha extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        $element = $this->element;
        if (!$element instanceof NumericcaptchaElement) {
            throw new BadConfigurationException('Numericcaptcha constraint requires a numericcaptcha element', 1731340812);
        }
        if ((int) $this->stringify($value) === $element->getDigit(1) + $element->getDigit(2)) {
            return true;
        }
        $element->reloadDigit(1);
        $element->reloadDigit(2);
        return false;
    }
}
