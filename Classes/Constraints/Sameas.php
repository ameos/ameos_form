<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

/**
 * Configuration: sameas, name of the element to compare with
 */
class Sameas extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        $element = $this->form->getElement($this->stringify($this->configuration['sameas'] ?? ''));
        // strict comparison: with "==", "1e3" and "1000" would be equal
        return $element !== false && $this->normalize($value) === $this->normalize($element->getValue());
    }

    private function normalize(mixed $value): mixed
    {
        return is_scalar($value) || $value instanceof \Stringable ? (string) $value : $value;
    }
}
