<?php

namespace Ameos\AmeosForm\Form\Search;

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
use Ameos\AmeosForm\Utility\UserUtility;

class ExtbaseForm extends \Ameos\AmeosForm\Form\Search
{

    /**
     * @var default clause
     */
    protected $defaultClause;
    
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Repository $repository repository
     */
    protected $respository;

    /**
     * @var array
     */
    protected $clauses;

    /**
     * @constuctor
     *
     * @param   string $identifier form identifier
     */
    public function __construct($identifier, \TYPO3\CMS\Extbase\Persistence\Repository $repository)
    {
        parent::__construct($identifier);
        $this->repository = $repository;
        $this->mode = 'search/extbase';
        $this->defaultClause = [];
    }

    /**
     * return extbase query result
     *
     * @param string|bool $orderby
     * @param string|bool $direction
     * @return Object
     */
    public function getResults($orderby = false, $direction = 'ASC')
    {
        foreach ($this->elements as $element) {
            if ($element->isSearchable()) {
                if ($element->getValue() == '') {
                    unset($this->clauses[$element->getName()]);
                }
                if (($clause = $element->getClause()) !== false) {
                    $this->clauses[$clause['elementname']] = $clause;
                }
            }
        }

        if ($this->storeSearchInSession === true) {
            if (UserUtility::isLogged()) {
                $GLOBALS['TSFE']->fe_user->setKey('user', 'form-' . $this->getIdentifier() . '-clauses', $this->clauses);
            } else {
                $GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->getIdentifier() . '-clauses', $this->clauses);
            }
            $GLOBALS['TSFE']->fe_user->storeSessionData();
        }
        
        $clauses = array_merge($this->clauses, $this->defaultClause);
        return $this->repository->findByClausesArray($clauses, $orderby, $direction);
    }

    /**
     * add where clause
     * @param array $clause where clause
     * @return Ameos\AmeosForm\Form\Search this
     */
    public function addWhereClause($clause)
    {
        $this->defaultClause[] = $clause;
        return $this;
    }
}
