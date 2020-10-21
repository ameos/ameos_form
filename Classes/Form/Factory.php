<?php

namespace Ameos\AmeosForm\Form;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use Ameos\AmeosForm\Domain\Repository\SearchableRepository;
use Ameos\AmeosForm\Form\Search as FormSearch;
use Ameos\AmeosForm\Form\Crud as FormCrud;

class Factory
{

    /**
     * create and return FORM
     * @return AbstractForm the form
     * @throws \Exception
     */
    public static function make(...$arguments)
    {
        if (
            is_string($arguments[0])
            && isset($arguments[1])
            && is_a($arguments[1], SearchableRepository::class)
        ) {
            return GeneralUtility::makeInstance(FormSearch\ExtbaseForm::class, $arguments[0], $arguments[1]);
        }

        if (
            is_string($arguments[0])
            && isset($arguments[1])
            && is_a($arguments[1], Repository::class)
        ) {
            throw new \Exception('Your repository must extends ' . SearchableRepository::class);
        }

        if (
            is_string($arguments[0])
            && isset($arguments[1])
            && is_a($arguments[1], AbstractEntity::class)
        ) {
            return GeneralUtility::makeInstance(FormCrud\ExtbaseForm::class, $arguments[0], $arguments[1]);
        }

        if (is_string($arguments[0]) && isset($arguments[1]) && is_string($arguments[1])) {
            if (isset($arguments[2])) {
                return GeneralUtility::makeInstance(FormCrud\ClassicForm::class, $arguments[0], $arguments[1], (int)$arguments[2]);
            } else {
                return GeneralUtility::makeInstance(FormCrud\ClassicForm::class, $arguments[0], $arguments[1]);
            }
        }

        if (sizeof($arguments) == 1) {
            return GeneralUtility::makeInstance(FormCrud::class, $arguments[0]);
        }

        throw new \Exception('Impossible to make the form with these arguments');
    }
}
