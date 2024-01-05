<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use Ameos\AmeosForm\Domain\Repository\Trait\SearchableRepository;

class Factory
{
    /**
     * create and return FORM
     *
     * @deprecated
     * @return Form the form
     */
    public static function make(...$arguments): Form
    {
        trigger_error('Use FormService or instanciate Form manually.', E_USER_DEPRECATED);
        $form = GeneralUtility::makeInstance(Form::class, $arguments[0]);
        if (isset($arguments[1]) && is_a($arguments[1], SearchableRepository::class)) {
            $form->attachRepository($arguments[1]);
        }

        if (isset($arguments[1]) && is_a($arguments[1], AbstractEntity::class)) {
            $form->attachEntity($arguments[1]);
        }

        return $form;
    }
}
