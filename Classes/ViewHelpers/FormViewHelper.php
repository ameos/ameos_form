<?php
namespace Ameos\AmeosForm\ViewHelpers;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class FormViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments() 
    {
        parent::initializeArguments();
        $this->registerArgument('form',    \Ameos\AmeosForm\Form\AbstractForm::class, 'form instance', false);
        $this->registerArgument('method',  'string', 'method attribute', false);
        $this->registerArgument('enctype', 'string', 'method attribute', false);
        $this->registerArgument('action',  'string', 'action attribute', false);
        $this->registerArgument('class',   'string', 'class attribute', false);
        $this->registerArgument('id',      'string', 'id attribute', false);
    }
    
    /**
     * Renders form
     *
     * @return string html
     */
    public function render() 
    {
        $form = $this->arguments['form'];
        $method  = $this->arguments['method']  == '' ? 'POST' : $this->arguments['method'];
		$enctype = $this->arguments['enctype'] == '' ? '' : ' enctype="' . $this->arguments['enctype'] . '"';
		$action  = $this->arguments['action']  == '' ? '' : ' action="' . $this->arguments['action'] .'"';
		$class   = $this->arguments['class']   == '' ? '' : ' class="' . $this->arguments['class'] . '"';
		$id      = $this->arguments['id']      == '' ? '' : ' id="' . $this->arguments['id'] . '"';

		if (!is_object($form)) {
			return 'Form is not valid. May be it\'s not assigned to the view.';
		}
		
		foreach ($form->getElements() as $elementName => $element) {
			$this->templateVariableContainer->add($elementName, $element);
		}
		if (strpos($form->getMode(), 'crud') !== FALSE) {
			$errors = $form->getErrors();
			if (!empty($errors)) {
				$this->templateVariableContainer->add('errors', $errors);
			}
		}
		
		$output = $this->renderChildren();
		
		foreach ($form->getElements() as $elementName => $element) {
			$this->templateVariableContainer->remove($elementName);
		}
		
		if (strpos($form->getMode(), 'crud') !== FALSE && !empty($errors)) {
			$this->templateVariableContainer->remove('errors');
		}
		
		if (TYPO3_MODE == 'FE') {
			if (!$form->isSubmitted()) {
				$csrftoken = GeneralUtility::shortMD5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
				$GLOBALS['TSFE']->fe_user->setKey('ses', $form->getIdentifier() . '-csrftoken', $csrftoken);
				$GLOBALS['TSFE']->storeSessionData();
			} else {
				$csrftoken = $GLOBALS['TSFE']->fe_user->getKey('ses', $form->getIdentifier() . '-csrftoken');
			}
		} else {
			$csrftoken = 'notoken';
		}
		
		$output = '<form method="' . $method . '" ' . $id . $enctype . $class . $action . '>' . $output . '
			<input type="hidden" id="' . $form->getIdentifier() . '-issubmitted" value="1" name="' . $form->getIdentifier() . '[issubmitted]" />';
		
		if ($form->csrftokenIsEnabled()) {
			$output.= '<input type="hidden" id="' . $form->getIdentifier() . '-csrftoken" value="' . $csrftoken . '" name="' . $form->getIdentifier() . '[csrftoken]" />';
		}
        if ($form->honeypotIsEnabled()) {
			$output.= '<span style="position:absolute;left:-500000px"><input type="text" id="' . $form->getIdentifier() . '-winnie" value="" name="' . $form->getIdentifier() . '[winnie]" /></span>';	
		}
		$output.= '</form>';
		return $output;
	}
}
