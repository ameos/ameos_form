<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class Unique extends ConstraintAbstract
{
    /**
     * return true if the element is sameas an another value
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        if ($value == '') {
            return true;
        }

        $defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->configuration['repository']->setDefaultQuerySettings($defaultQuerySettings);

        $field   = $this->element->getName();
        $method  = 'findBy' . ucfirst($field);
        $records = $this->configuration['repository']->$method($value);
        $isValid = true;
        foreach ($records as $record) {
            if (
                $this->form->getAttachedEntity()
                && (int)$record->getUid() !== (int)$this->form->getAttachedEntity()->getUid()
            ) {
                $isValid = false;
            }
        }
        return $isValid;
    }
}
