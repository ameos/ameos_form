<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Constraints\Numericcaptcha as NumericcaptchaConstraint;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class Numericcaptcha extends ElementAbstract
{
    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (!$this->form->isSubmitted()) {
            $this->reloadDigit(1);
            $this->reloadDigit(2);
        }
        $errorMessage = isset($configuration['errormessage'])
            ? $configuration['errormessage']
            : 'Numeric captcha is not valid';
        $constraint = GeneralUtility::makeInstance(NumericcaptchaConstraint::class, $errorMessage, [], $this, $form);
        $this->addConstraint($constraint);
    }

    public function reloadDigit($key): void
    {
        $sessionKey = 'form-' . $this->form->getIdentifier() . '-' . $this->getHtmlId() . '-digit-' . $key;

        /** @var FrontendUserAuthentication */
        $frontendUser = $this->form->getCurrentRequest()->getAttribute('frontend.user');
        $frontendUser->setAndSaveSessionData($sessionKey, false);

        $this->getDigit($key);
    }

    public function getDigit($key): int
    {
        $sessionKey = 'form-' . $this->form->getIdentifier() . '-' . $this->getHtmlId() . '-digit-' . $key;

        /** @var FrontendUserAuthentication */
        $frontendUser = $this->form->getCurrentRequest()->getAttribute('frontend.user');
        $digit = $frontendUser->getSessionData($sessionKey);

        if (!$digit) {
            $digit = rand(1, 9);
            $frontendUser->setAndSaveSessionData($sessionKey, $digit);
        }
        return $digit;
    }

    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        $sid = md5(uniqid());
        return $this->renderLabel() . $this->renderCaptchaInput();
    }


    /**
     * render captcha picture
     *
     * @return string
     */
    protected function renderCaptchaInput(): string
    {
        return '<input type="text" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getValue() . '"'
            . $this->getAttributes() . ' />';
    }

    protected function renderLabel(): string
    {
        return '<label for="' . $this->getHtmlId() . '">' . $this->renderOperation() . '</label>';
    }

    protected function renderOperation(): string
    {
        return $this->getDigit(1) . ' + ' . $this->getDigit(2);
    }

    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        $data['input']   = $this->renderCaptchaInput();
        $data['label']   = $this->renderLabel();
        $data['operation']   = $this->renderOperation();
        return $data;
    }
}
