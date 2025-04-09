<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\ViewHelpers;

use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FormViewHelper extends AbstractViewHelper
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
        $this->registerArgument('form', Form::class, 'form instance', false);
        $this->registerArgument('method', 'string', 'method attribute', false);
        $this->registerArgument('enctype', 'string', 'method attribute', false);
        $this->registerArgument('action', 'string', 'action attribute', false);
        $this->registerArgument('class', 'string', 'class attribute', false);
        $this->registerArgument('id', 'string', 'id attribute', false);
    }

    /**
     * Renders form
     *
     * @return string html
     */
    public function render()
    {
        $form = $this->arguments['form'];
        $method  = $this->arguments['method']  == '' ? 'POST' : $this->arguments['method'];
        $enctype = $this->arguments['enctype'] == ''
            ? ' enctype="multipart/form-data"'
            : ' enctype="' . $this->arguments['enctype'] . '"';
        $action  = $this->arguments['action']  == '' ? '' : ' action="' . $this->arguments['action'] . '"';
        $class   = $this->arguments['class']   == '' ? '' : ' class="' . $this->arguments['class'] . '"';
        $id      = $this->arguments['id']      == '' ? '' : ' id="' . $this->arguments['id'] . '"';

        if (!is_object($form)) {
            return 'Form is not valid. May be it\'s not assigned to the view.';
        }

        foreach ($form->getElements() as $elementName => $element) {
            $this->templateVariableContainer->add($elementName, $element);
        }

        $errors = $form->getErrors();
        if (!empty($errors)) {
            $this->templateVariableContainer->add('errors', $errors);
        }

        $output = $this->renderChildren();

        foreach ($form->getElements() as $elementName => $element) {
            $this->templateVariableContainer->remove($elementName);
        }

        $this->templateVariableContainer->remove('errors');

        /** @var ServerRequest */
        $request = $GLOBALS['TYPO3_REQUEST'];
        if (ApplicationType::fromRequest($request)->isFrontend()) {
            /** @var FrontendUserAuthentication */
            $frontendUser = $request->getAttribute('frontend.user');

            if (!$form->isSubmitted()) {
                $csrftoken = sha1(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
                $frontendUser->setAndSaveSessionData($form->getIdentifier() . '-csrftoken', $csrftoken);
            } else {
                $csrftoken = $frontendUser->getSessionData($form->getIdentifier() . '-csrftoken');
            }
        } else {
            $csrftoken = 'notoken';
        }

        $output = '<form method="' . $method . '" ' . $id . $enctype . $class . $action . '>' . $output . '
			<input type="hidden" id="' . $form->getIdentifier() . '-issubmitted" '
                . 'value="1" '
                . 'name="' . $form->getIdentifier() . '[issubmitted]" />';

        if ($form->csrftokenIsEnabled()) {
            $output .= '<input type="hidden" '
                . 'id="' . $form->getIdentifier() . '-csrftoken" '
                . 'value="' . $csrftoken . '" '
                . 'name="' . $form->getIdentifier() . '[csrftoken]" />';
        }
        if ($form->honeypotIsEnabled()) {
            $output .= '<span style="position:absolute;left:-500000px">'
                . '<input type="text" id="' . $form->getIdentifier() . '-winnie" '
                . 'value="" name="' . $form->getIdentifier() . '[winnie]" /></span>';
        }
        $output .= '</form>';
        return $output;
    }
}
