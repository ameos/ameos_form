<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Elements\Date;
use Ameos\AmeosForm\Elements\Upload;

class Required extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        if ($this->element instanceof Upload && is_array($value) && is_array($value['upload'] ?? null)) {
            return true;
        }
        if ($this->element instanceof Date) {
            // option input=timestamp stores an int
            return $value instanceof \DateTimeInterface || is_int($value);
        }
        return match (true) {
            $value === null => false,
            is_array($value) => $value !== [],
            is_scalar($value), $value instanceof \Stringable => trim((string) $value) !== '',
            default => true,
        };
    }
}
