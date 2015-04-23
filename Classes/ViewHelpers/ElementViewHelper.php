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
