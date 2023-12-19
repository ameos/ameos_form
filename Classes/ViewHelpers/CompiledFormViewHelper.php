<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CompiledFormViewHelper extends AbstractViewHelper
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
