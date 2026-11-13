<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\ViewHelpers;

use Ameos\AmeosForm\Form\Form;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Render the form tag. The form elements (and "errors") are available as variables inside the tag:
 *
 * <form:form form="{form}">{myElement}</form:form>
 */
final class FormViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('form', Form::class, 'form instance', false);
        $this->registerArgument('method', 'string', 'method attribute', false, 'POST');
        $this->registerArgument('enctype', 'string', 'enctype attribute', false, 'multipart/form-data');
        $this->registerArgument('action', 'string', 'action attribute', false, '');
        $this->registerArgument('class', 'string', 'class attribute', false, '');
        $this->registerArgument('id', 'string', 'id attribute', false, '');
    }

    public function render(): string
    {
        $form = $this->arguments['form'];
        if (!$form instanceof Form) {
            return 'Form is not valid. May be it\'s not assigned to the view.';
        }

        $variableProvider = $this->renderingContext?->getVariableProvider();
        if ($variableProvider === null) {
            return '';
        }
        foreach ($form->getElements() as $elementName => $element) {
            $variableProvider->add($elementName, $element);
        }
        $errors = $form->getErrors();
        if ($errors !== []) {
            $variableProvider->add('errors', $errors);
        }

        $content = $this->renderChildren();

        foreach (array_keys($form->getElements()) as $elementName) {
            $variableProvider->remove($elementName);
        }
        $variableProvider->remove('errors');

        return '<form' . $this->renderAttributes() . '>' . (is_scalar($content) ? (string) $content : '')
            . $this->renderHiddenFields($form)
            . '</form>';
    }

    private function renderAttributes(): string
    {
        $defaults = ['method' => 'POST', 'id' => '', 'enctype' => 'multipart/form-data', 'class' => '', 'action' => ''];
        $attributes = '';
        foreach ($defaults as $name => $default) {
            $value = $this->arguments[$name] ?? '';
            $value = is_string($value) && $value !== '' ? $value : $default;
            if ($value !== '') {
                $attributes .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false) . '"';
            }
        }
        return $attributes;
    }

    private function renderHiddenFields(Form $form): string
    {
        $identifier = htmlspecialchars($form->getIdentifier(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $output = '<input type="hidden" id="' . $identifier . '-issubmitted" value="1" name="' . $identifier . '[issubmitted]" />';
        if ($form->csrftokenIsEnabled()) {
            $output .= '<input type="hidden" id="' . $identifier . '-csrftoken" '
                . 'value="' . htmlspecialchars($form->getCsrfToken(), ENT_QUOTES | ENT_HTML5, 'UTF-8') . '" '
                . 'name="' . $identifier . '[csrftoken]" />';
        }
        if ($form->honeypotIsEnabled()) {
            $output .= '<span style="position:absolute;left:-500000px">'
                . '<input type="text" id="' . $identifier . '-winnie" value="" name="' . $identifier . '[winnie]" /></span>';
        }
        return $output;
    }
}
