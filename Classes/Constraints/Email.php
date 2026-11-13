<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Email extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        return is_string($value) && GeneralUtility::validEmail($value);
    }
}
