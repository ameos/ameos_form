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

class ElementViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('element', \Ameos\AmeosForm\Elements\ElementInterface::class, 'form element', false);
        $this->registerArgument('class', 'string', 'class attribute', false);
        $this->registerArgument('errorClass', 'string', 'class attribute when error', false);
        $this->registerArgument('placeholder', 'string', 'placeholder attribute', false);
        $this->registerArgument('style', 'string', 'style attribute', false);
        $this->registerArgument('custom', 'string', 'custom attribute', false);
    }

    /**
     * Renders form
     *
     * @return string html
     */
    public function render()
    {
        if (!is_a($this->arguments['element'], '\\Ameos\\AmeosForm\\Elements\\ElementInterface')) {
            return '';
        }

        if ($this->arguments['class'] !== '') {
            $this->arguments['element']->addConfiguration('class', $this->arguments['class']);
        }
        if ($this->arguments['errorClass'] !== '') {
            $this->arguments['element']->addConfiguration('errorClass', $this->arguments['errorClass']);
        }
        if ($this->arguments['placeholder'] !== '') {
            $this->arguments['element']->addConfiguration('placeholder', $this->arguments['placeholder']);
        }
        if ($this->arguments['style'] !== '') {
            $this->arguments['element']->addConfiguration('style', $this->arguments['style']);
        }
        if ($this->arguments['custom'] !== '') {
            $this->arguments['element']->addConfiguration('custom', $this->arguments['custom']);
        }

        return $this->arguments['element']->toHtml();
    }
}
