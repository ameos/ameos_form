<?php

namespace Ameos\AmeosForm\ViewHelpers;

class ElementViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Renders form
     *
     * @param \Ameos\AmeosForm\Elements\ElementAbstract $form the form
     * @param string $class css class
     * @param string $errorClass css error class
     * @param string $placeholder placeholder
     * @return string html
     */
    public function render($element, $class = '', $errorClass = '', $placeholder = '') {
		if(!is_a($element, '\\Ameos\\AmeosForm\\Elements\\ElementInterface')) {
			return '';
		}

		if($class !== '')       { $element->addConfiguration('class', $class); }
		if($errorClass !== '')  { $element->addConfiguration('errorClass', $errorClass); }
		if($placeholder !== '') { $element->addConfiguration('placeholder', $placeholder); }
		
		return $element->toHtml();
    }
}
