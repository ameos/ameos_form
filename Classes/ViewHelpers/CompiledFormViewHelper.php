<?php

namespace Ameos\AmeosForm\ViewHelpers;

class CompiledFormViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Renders form
     *
     * @param \Ameos\AmeosForm\Form $form the form
     * @return string html
     */
    public function render($form) {
		return $form->toHtml();
    }
}
