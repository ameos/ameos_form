<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ElementViewHelper extends AbstractViewHelper
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
