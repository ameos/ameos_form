<?php
namespace Ameos\AmeosForm\Utility;

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

class ErrorManager {

	/**
	 * @var array $errors
	 */
	protected $errors;

	/**
	 * @var \Ameos\AmeosForm\Form $form form
	 */
	protected $form;
	
	/**
	 * @var array $elementsConstraintsChecked true if elements constraints checked
	 */
	protected $elementsConstraintsAreChecked;
	
	/**
	 * @constructor
	 */
	public function __construct($form) {
		$this->errors = [];
		$this->elementsConstraintsAreChecked = [];
		$this->form = $form;
	}

	/**
	 * add an error
	 * @param string|array $errors errors
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|NULL $element element
	 */
	public function add($errors, $element = NULL) {
		$elementName = $element === NULL ? 'system' : $element->getName();
		
		if(is_string($errors)) {
			$errors = [$errors];
		}
		
		if(!array_key_exists($elementName, $this->errors)) {
			$this->errors[$elementName] = [];
		}

		$this->errors[$elementName] = array_merge($this->errors[$elementName], $errors);
	}

	/**
	 * return element's error (if no element set, return all errors)
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|string|NULL $element element
	 * @return array
	 */
	public function getErrors($element = NULL) {
		if($element === NULL) {
			return $this->getAllErrors();
		}
		
		$this->determineErrorsForElement($element);
		
		$elementName = is_string($element) ? $element : $element->getName();		
		
		if(array_key_exists($elementName, $this->errors)) {
			return $this->errors[$elementName];
		}
		
		return array();
	}

	/**
	 * return all errors
	 * @return array
	 */
	public function getAllErrors() {
		$this->determineErrors();
		
		return $this->errors;
	}
	
	/**
	 * return all errors merged
	 * @return array
	 */
	public function getAllErrorsMerged() {
		$this->determineErrors();
		
		$errors = [];
		foreach($this->errors as $key => $elementErrors) {
			$errors = array_merge($errors, $elementErrors);
		}
		return $errors;
	}
	
	/**
	 * return true if no error
	 * @return bool
	 */ 
	public function isValid() {
		$this->determineErrors();
		
		return empty($this->errors);
	}
	
	/**
	 * return true if element if valid
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|string $element element
	 * @return bool
	 */
	public function elementIsValid($element) {
		$this->determineErrorsForElement($element);
		
		$elementName = is_string($element) ? $element : $element->getName();		
		
		if(!array_key_exists($elementName, $this->errors)) {
			return TRUE;
		}
		
		if(empty($this->errors[$elementName])) {
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * determine errors
	 */
	protected function determineErrors() {
		foreach($this->form->getElements() as $element) {
			$this->determineErrorsForElement($element);
		}
	}
	
	/**
	 * determine errors for an element
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|string $element element
	 */
	protected function determineErrorsForElement($element) {
		$elementName = is_string($element) ? $element : $element->getName();		
		
		if(!in_array($elementName, $this->elementsConstraintsAreChecked)) {
			$this->form->get($elementName)->determineErrors();
			$this->elementsConstraintsAreChecked[] = $elementName;
		}
	}
}
