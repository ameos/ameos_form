<?php

namespace Ameos\AmeosForm\Form\Search;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassicForm extends \Ameos\AmeosForm\Form\Search {

	/**
	 * @var string $query query
	 */
	protected $query;

	/**
	 * @var string $query query limit
	 */
	protected $queryLimit;

	/**
	 * @constuctor
	 *
	 * @param	string $identifier form identifier
	 * @param	string $query query
	 */
	public function __construct($identifier, $query) {
		parent::__construct($identifier);
		$this->mode       = 'search/classic';
		$this->query      = $query;;		
		$this->queryLimit = ' LIMIT 0,10';
	}

	/**
	 * return extbase query result
	 *
	 * @param string|bool $orderby
	 * @param string|bool $direction
	 * @return resource
	 */
	public function getResults($orderby = FALSE, $direction = 'ASC') {
		foreach($this->elements as $element) {
			if($element->isSearchable()) {
				if($element->getValue() == '') {
					unset($this->clauses[$element->getName()]);
				}
				if(($clause = $element->getClause()) !== FALSE) {
					$this->clauses[$clause['elementname']] = $clause;
				}
			}
		}
		
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->getIdentifier() . '-clauses', $this->clauses);
		$GLOBALS['TSFE']->storeSessionData();
		
		$clauses = '';
		foreach($this->clauses as $clause) {
			$clauses.= ' AND ' . $this->makeWhereClause($clause);
		}

		if($orderby) {
			$order = ' ORDER BY ' . $orderby . ' ' . $direction;
		} else {
			$order = '';
		}

		return $GLOBALS['TYPO3_DB']->sql_query($this->query . $clauses . $order . $this->querylimit);
	}

	/**
	 * make query where clause part
	 * @param array $clause where clause information
	 * @return string
	 */
	protected function makeWhereClause($clause) {
		return $clause['field'] . ' ' . $clause['type'] . ' \'' . $clause['value'] . '\'';
	}
}
