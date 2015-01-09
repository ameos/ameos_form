<?php

namespace Ameos\AmeosForm\ViewHelpers;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class FormViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Renders form
     *
     * @param \Ameos\AmeosForm\Form\AbstractForm $form the form
     * @param string $method method
     * @param string $enctype enctype
     * @param string $action action
     * @param string $class class
     * @param string $id id
     * @return string html
     */
    public function render($form, $method = 'post', $enctype = 'multipart/form-data', $action = '', $class = '', $id = '') {
		$enctype = $enctype == '' ? '' : ' enctype="' . $enctype . '"';
		$action  = $action  == '' ? '' : ' action="' . $action .'"';
		$class   = $class   == '' ? '' : ' class="' . $class . '"';
		$id      = $id      == '' ? '' : ' id="' . $id . '"';
		
		foreach($form->getElements() as $elementName => $element) {
			$this->templateVariableContainer->add($elementName, $element);
		}
		if(strpos($form->getMode(), 'crud') !== FALSE) {
			$errors = $form->getErrors();
			if(!empty($errors)) {
				$this->templateVariableContainer->add('errors', $errors);
			}
		}
		
		$output = $this->renderChildren();
		
		foreach($form->getElements() as $elementName => $element) {
			$this->templateVariableContainer->remove($elementName);
		}
		
		if(strpos($form->getMode(), 'crud') !== FALSE && !empty($errors)) {
			$this->templateVariableContainer->remove('errors');
		}

		$csrftoken = GeneralUtility::shortMD5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
		$GLOBALS['TSFE']->fe_user->setKey('ses', $form->getIdentifier() . '-csrftoken', $csrftoken);
		$GLOBALS['TSFE']->storeSessionData(); 
		return '<form method="' . $method . '" ' . $id . $enctype . $class . $action . '>' . $output . '
			<input type="hidden" id="' . $form->getIdentifier() . '-issubmitted" value="1" name="' . $form->getIdentifier() . '[issubmitted]" />
			<input type="hidden" id="' . $form->getIdentifier() . '-csrftoken" value="' . $csrftoken . '" name="' . $form->getIdentifier() . '[csrftoken]" />
			</form>';
    }
}
