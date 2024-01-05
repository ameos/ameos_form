<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Service;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class FormService
{
    /**
     * create and return FORM
     *
     * @return Form the form
     */
    public function create(...$arguments): Form
    {
        $form = GeneralUtility::makeInstance(Form::class, $arguments[0]);
        if (isset($arguments[1]) && is_a($arguments[1], SearchableRepositoryInterface::class)) {
            $form->attachRepository($arguments[1]);
        }

        if (isset($arguments[1]) && is_a($arguments[1], AbstractEntity::class)) {
            $form->attachEntity($arguments[1]);
        }

        return $form;
    }
}
