<?php

namespace Ameos\AmeosForm\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\Events;
use Ameos\AmeosForm\Utility\UserUtility;

abstract class Search extends \Ameos\AmeosForm\Form\AbstractForm {

	/**
	 * @var bool $storeSearchInSession
	 */
	protected $storeSearchInSession = TRUE;
	
	/**
	 * @constuctor
	 *
	 * @param	string $identifier form identifier
	 */
	public function __construct($identifier) {
		parent::__construct($identifier);
		if(UserUtility::isLogged()) {
			$this->clauses = $GLOBALS['TSFE']->fe_user->getKey('user', 'form-' . $this->getIdentifier() . '-clauses');
		} else {
			$this->clauses = $GLOBALS['TSFE']->fe_user->getKey('ses', 'form-' . $this->getIdentifier() . '-clauses');
		}

		if(!is_array($this->clauses)) {
			$this->clauses = [];
		}
	}

	/**
	 * set if the search criterias are stored in session
	 * @param	bool	$storeSearchInSession
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function storeSearchInSession($storeSearchInSession = TRUE) {
		$this->storeSearchInSession = $storeSearchInSession;
		return $this;
	}
	
	/**
	 * add element fo the form
	 * 
	 * @param	string	$type element type
	 * @param	string	$name element name
	 * @param	array	$configuration element configuration
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function add($name, $type = '', $configuration = [], $overrideFunction = FALSE) {
		parent::add($name, $type, $configuration);
		if($overrideFunction !== FALSE) {
			$this->elements[$name]->setOverrideClause($overrideFunction);	
		}
		return $this;
	}

	/**
	 * set value from session
	 */
	public function setValueFromSession() {
		foreach($this->clauses as $clause) {
			if(($element = $this->getElement($clause['elementname'])) !== FALSE) {
				$element->setValue($clause['elementvalue']);
			}
		}
	}
}
