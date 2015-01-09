<?php

namespace Ameos\AmeosForm\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\Events;

class Crud extends \Ameos\AmeosForm\Form\AbstractForm {

	/**
	 * add validator
	 * 
	 * @param	string	$elementName element name
	 * @param	string	$type validator type	 
	 * @param	string	$message message
	 * @param	array	$configuration configuration
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function validator($elementName, $type, $message, $configuration = []) {
		foreach($this->elements as $elementKey => $element) {
			if($elementName == $elementKey) {
				switch($type) {
					case 'email':         $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Email',         $message, $configuration, $this->getElement($elementName), $this); break;
					case 'sameas':        $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Sameas',        $message, $configuration, $this->getElement($elementName), $this); break;
					case 'unique':        $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Unique',        $message, $configuration, $this->getElement($elementName), $this); break;
					case 'fileextension': $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Fileextension', $message, $configuration, $this->getElement($elementName), $this); break;
					case 'filesize':      $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Filesize',      $message, $configuration, $this->getElement($elementName), $this); break;
					case 'captcha':       $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Captcha',       $message, $configuration, $this->getElement($elementName), $this); break;
					case 'required':
					default:              $validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Required',      $message, $configuration, $this->getElement($elementName), $this); break;
				}
		
				$element->validator($validator);
			}
		}
		
		return $this;
	}

	/**
	 * return true if the form is valide
	 *
	 * @return 	bool true if is a valid form
	 */
	public function isValid() {
		$hasError = FALSE;
		foreach($this->elements as $element) {
			if(!$element->isValid()) {
				$hasError = TRUE;
				$this->errors = array_merge($this->errors, $element->getErrors());
				$this->errorsByElement[$element->getName()] = $element->getErrors();
			}
		}

		if(!$hasError) {
			Events::getInstance($this->getIdentifier())->trigger('form_is_valid');
		}
		
		return !$hasError;
	}

	/**
	 * Return errors
	 *
	 * @return	array errors
	 */
	public function getErrors() {
		// voir pour un ErrorManager accessible depuis les validateurs et tout ça. Un truc plus propre que transmettre des tableeau
		// du genre une classe ErrorManager avec un méthode add error, une methode get, une methode hasError pour une condiion fluid. Un truc simple
		return $this->errors;
	}

	/**
	 * Return errors by elements
	 *
	 * @return	array errors
	 */
	public function getErrorsByElement() {
		return $this->errorsByElement;
	}

	/**
	 * Return errors by elements
	 *
	 * @return	array errors
	 */
	public function getErrorsFormElement($elementName) {
		if(array_key_exists($elementName, $this->errorsByElement)) {
			return $this->errorsByElement[$elementName];
		}
		return FALSE;
	}
}
