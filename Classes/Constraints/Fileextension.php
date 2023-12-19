<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Fileextension extends ConstraintAbstract
{
    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        if (!is_array($value) && empty($value)) {
            return true;
        }

        if (is_array($value)) {
            $pathfinfo = pathinfo($value['name']);
            return GeneralUtility::inList($this->configuration['allowed'], strtolower($pathfinfo['extension']));
        }

        return false;
    }
}
