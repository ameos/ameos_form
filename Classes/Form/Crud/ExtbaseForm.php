<?php

namespace Ameos\AmeosForm\Form\Crud;

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
use Ameos\AmeosForm\Utility\Events;

class ExtbaseForm extends \Ameos\AmeosForm\Form\Crud
{
    
    /**
     * @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model model
     */
    protected $model;

    /**
     * @constuctor
     *
     * @param   string $identifier form identifier
     * @param   \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model model
     */
    public function __construct($identifier, \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model)
    {
        parent::__construct($identifier);
        $this->mode  = 'crud/extbase';
        $this->model = $model;
    }

    /**
     * Return model
     *
     * @return  \TYPO3\CMS\Extbase\DomainObject\AbstractEntity model
     */
    public function getModel()
    {
        if ($this->getMode() == 'crud/extbase') {
            return $this->model;
        }
        return null;
    }
}
