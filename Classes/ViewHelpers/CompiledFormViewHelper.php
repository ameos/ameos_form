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

class CompiledFormViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper 
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
        $this->registerArgument('form', \Ameos\AmeosForm\Form\AbstractForm::class, 'form instance', false);
    }

    /**
     * Renders form
     *
     * @return string html
     */
    public function render() 
    {
		return $this->arguments['form']->toHtml();
    }
}
