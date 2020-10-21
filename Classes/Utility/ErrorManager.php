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

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class ErrorManager 
{

	/**
	 * @var \TYPO3\CMS\Core\Messaging\FlashMessageService
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $flashMessageService;
	
	/**
	 * @var \TYPO3\CMS\Core\Messaging\FlashMessageQueue
	 */
	protected $flashMessageQueue;
	
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
	 * @var bool $mustCheckConstraints
	 */
	protected $checkConstraints;
	
	/**
	 * @var bool $enableFlashMessage enable flash message
	 */
	protected $enableFlashMessage = true;
	
	/**
	 * @var bool $useLegacyFlashMessageHandling for TYPO3 6.0 (@deprecated since 6.1, will be removed 2 versions later)
	 */
	protected $useLegacyFlashMessageHandling = false;
	
	/**
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($form) 
	{
		$this->errors = [];
		$this->elementsConstraintsAreChecked = [];
		$this->enableFlashMessage = true;
		$this->useLegacyFlashMessageHandling = false;
		$this->form = $form;
	}

	/**
	 * enable flash message
	 * @return \Ameos\AmeosForm\Form\AbstractForm
	 */
	public function enableFlashMessage() 
	{
		$this->enableFlashMessage = true;
		return $this;
	}

	/**
	 * disable flash message
	 * @return \Ameos\AmeosForm\Form\AbstractForm
	 */
	public function disableFlashMessage() 
	{
		$this->enableFlashMessage = false;
		return $this;
	}

	/**
	 * return TRUE if flash message is enabled
	 * @return bool
	 */
	public function flashMessageIsEnabled() 
	{
		return $this->enableFlashMessage;
	}
	
	/**
	 * use Legacy Flash Message Handling
	 * @return \Ameos\AmeosForm\Form\AbstractForm
	 */
	public function useLegacyFlashMessageHandling() 
	{
		$this->useLegacyFlashMessageHandling = true;
		return $this;
	}
	
	/**
	 * don't use Legacy Flash Message Handling
	 * @return \Ameos\AmeosForm\Form\AbstractForm
	 */
	public function dontUseLegacyFlashMessageHandling() 
	{
		$this->useLegacyFlashMessageHandling = false;
		return $this;
	}

	/**
	 * add an error
	 * @param string|array $errors errors
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|NULL $element element
	 */
	public function add($errors, $element = null) 
	{
		$elementName = $element === null ? 'system' : $element->getName();
		
		if (is_string($errors)) {
			$errors = [$errors];
		}
		
		if (!array_key_exists($elementName, $this->errors)) {
			$this->errors[$elementName] = [];
		}

		$this->errors[$elementName] = array_merge($this->errors[$elementName], $errors);
		
		if ($this->flashMessageIsEnabled()) {
			foreach ($errors as $error) {
				$flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $error, '', AbstractMessage::ERROR);
				$this->getFlashMessageQueue()->enqueue($flashMessage);
			}
		}
	}
	
	/**
	 * @return \TYPO3\CMS\Core\Messaging\FlashMessageQueue
	 */
	public function getFlashMessageQueue() 
	{
		if (!$this->flashMessageQueue instanceof \TYPO3\CMS\Core\Messaging\FlashMessageQueue) {
			if ($this->useLegacyFlashMessageHandling) {
				$this->flashMessageQueue = $this->flashMessageService->getMessageQueueByIdentifier();
			} else {
				$this->flashMessageQueue = $this->flashMessageService->getMessageQueueByIdentifier('extbase.flashmessages.' . $this->form->getIdentifier());	
			}
		}

		return $this->flashMessageQueue;
	}

	/**
	 * return element's error (if no element set, return all errors)
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|string|NULL $element element
	 * @return array
	 */
	public function getErrors($element = null) 
	{
		if (!$this->checkConstraints()) {
			return array();
		}
		
		if ($element === NULL) {
			return $this->getAllErrors();
		}
		
		$this->determineErrorsForElement($element);
		
		$elementName = is_string($element) ? $element : $element->getName();		
		
		if (array_key_exists($elementName, $this->errors)) {
			return $this->errors[$elementName];
		}
		
		return array();
	}

	/**
	 * return all errors
	 * @return array
	 */
	public function getAllErrors() 
	{
		if (!$this->checkConstraints()) {
			return array();
		}
		
		$this->determineErrors();
		
		return $this->errors;
	}
	
	/**
	 * return all errors merged
	 * @return array
	 */
	public function getAllErrorsMerged() 
	{
		if (!$this->checkConstraints()) {
			return array();
		}
		
		$this->determineErrors();
		
		$errors = [];
		foreach ($this->errors as $key => $elementErrors) {
			$errors = array_merge($errors, $elementErrors);
		}
		return $errors;
	}
	
	/**
	 * return true if no error
	 * @return bool
	 */ 
	public function isValid() 
	{
		if (!$this->checkConstraints()) {
			return true;
		}
		
		$this->determineErrors();
		
		return empty($this->errors);
	}
	
	/**
	 * return true if element if valid
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|string $element element
	 * @return bool
	 */
	public function elementIsValid($element) 
	{
		if (!$this->checkConstraints()) {
			return true;
		}
		
		$this->determineErrorsForElement($element);
		
		$elementName = is_string($element) ? $element : $element->getName();		
		
		if (!array_key_exists($elementName, $this->errors)) {
			return true;
		}
		
		if (empty($this->errors[$elementName])) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * determine errors
	 */
	protected function determineErrors() 
	{
		foreach ($this->form->getElements() as $element) {
			$this->determineErrorsForElement($element);
		}
	}
	
	/**
	 * determine errors for an element
	 * @param Ameos\AmeosForm\Elements\ElementAbstract|string $element element
	 */
	protected function determineErrorsForElement($element) 
	{
		$elementName = is_string($element) ? $element : $element->getName();		
		
		if (!in_array($elementName, $this->elementsConstraintsAreChecked)) {
			$this->form->get($elementName)->determineErrors();			
			$this->elementsConstraintsAreChecked[] = $elementName;
		}
	}
	
	/**
	 * return true if must check constraints
	 * @return bool
	 */
	protected function checkConstraints() 
	{
		if ($this->checkConstraints === null) {
			$submitter = $this->form->getSubmitter();
			if (is_object($submitter)) {
				$this->checkConstraints = $submitter->checkConstraints();
			} else {
				$this->checkConstraints = true;	
			}			
		}
		return $this->checkConstraints;
	}
}
