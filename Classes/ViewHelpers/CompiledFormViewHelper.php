<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\ViewHelpers;

use Ameos\AmeosForm\Form\Form;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Render all the elements of a form (to be used inside <form:form>)
 */
final class CompiledFormViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('form', Form::class, 'form instance', false);
    }

    public function render(): string
    {
        $form = $this->arguments['form'];
        return $form instanceof Form ? $form->toHtml() : '';
    }
}
