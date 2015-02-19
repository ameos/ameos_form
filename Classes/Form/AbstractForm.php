<?php

namespace Ameos\AmeosForm\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\FormUtility;
use Ameos\AmeosForm\Utility\Events;

abstract class AbstractForm {

	/**
	 * @var string $identifer identifier
	 */
	protected $identifier;

	/**
	 * @var array $elements elements
	 */
	protected $elements;

	/**
	 * @var string $mode mode
	 */
	protected $mode;
	
	/**
	 * @constuctor
	 *
	 * @param	string $identifier form identifier
	 */
	public function __construct($identifier) {
		$this->elements   = [];
		$this->identifier = $identifier;
	}
	
	/**
	 * return identifier
	 * @return string identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * return elements
	 * @return array elements
	 */
	public function getElements() {
		return $this->elements;
	}

	/**
	 * return eleemnt
	 * @param	string $name element name
	 * @return 	\Ameos\AmeosForm\Elements\ElementInterface
	 */
	public function getElement($name) {
		return array_key_exists($name, $this->elements) ? $this->elements[$name] : FALSE;
	}

	/**
	 * add element fo the form
	 * 
	 * @param	string	$type element type
	 * @param	string	$name element name
	 * @param	string	$configuration element configuration
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function add($name, $type = '', $configuration = []) {
		$absolutename = $this->identifier . '[' . $name . ']';
		$element = FormUtility::makeElementInstance($absolutename, $name, $type, $configuration, $this);

		if($this->getMode() == 'search/extbase' || $this->getMode() == 'search/classic') {
			if(array_key_exists($name, $this->clauses)) {
				$element->setValue($this->clauses[$name]['elementvalue']);
			}
		}

		$this->elements[$name] = $element;
		return $this;
	}
	
	/**
	 * Return mode
	 *
	 * @return	string mode
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$csrftoken = GeneralUtility::shortMD5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
		$GLOBALS['TSFE']->fe_user->setKey('ses', $this->getIdentifier() . '-csrftoken', $csrftoken);
		$GLOBALS['TSFE']->storeSessionData();
		
		$html = '<form method="post">';
		foreach($this->elements as $element) {
			$html.= $element->toHtml();
		}
		$html.= '<input type="hidden" id="' . $this->getIdentifier() . '-issubmitted" value="1" name="' . $this->getIdentifier() . '[issubmitted]" />';
		$html.= '<input type="hidden" id="' . $this->getIdentifier() . '-csrftoken" value="' . $csrftoken . '" name="' . $this->getIdentifier() . '[csrftoken]" />';
		$html.= '</form>';
		return $html;
	}

	/**
	 * bind request to the form
	 * @param	$request \TYPO3\CMS\Extbase\Mvc\Request|array the request
	 * @return	\Ameos\AmeosForm\Form this 
	 */ 
	public function bindRequest($request) {
		if(!is_array($request) && !is_a($request, '\\TYPO3\\CMS\\Extbase\\Mvc\\Request')) {
			throw new \Exception('request must be an array or an extbase request (\\TYPO3\\CMS\\Extbase\\Mvc\\Request)');
		}
	/*
		good idea : but problem with double posting. May be add a simple message and not an exception
		if($request->getArgument('csrftoken') != $GLOBALS['TSFE']->fe_user->getKey('ses', $this->getIdentifier() . '-csrftoken')) {
			throw new \Exception('Forbidden: invalid csrf token');
		}	*/

		$requestDatas = is_a($request, '\\TYPO3\\CMS\\Extbase\\Mvc\\Request') ? $request->getArguments() : $request;
		foreach($this->elements as $elementName => $element) {			
			if(array_key_exists($elementName, $requestDatas)) {
				$element->setValue($requestDatas[$elementName]);
			} else {
				$element->setValue('');
			}
		}
		
		return $this;
	}

	/**
	 * is submitted
	 * @return bool
	 */
	public function isSubmitted() {
		$post = GeneralUtility::_POST($this->identifier);
		return isset($post['issubmitted']) && $post['issubmitted'] == 1;
	}
}
