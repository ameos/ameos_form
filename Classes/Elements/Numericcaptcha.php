<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Constraints\Numericcaptcha as NumericcaptchaConstraint;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Simple "a + b" captcha, digits are stored in the frontend user session
 */
class Numericcaptcha extends ElementAbstract
{
    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        if (!$this->form->isSubmitted()) {
            $this->reloadDigit(1);
            $this->reloadDigit(2);
        }

        $errorMessage = $this->stringify($this->configuration['errormessage'] ?? 'Numeric captcha is not valid');
        $this->addConstraint(new NumericcaptchaConstraint($errorMessage, [], $this, $form));
    }

    public function reloadDigit(int $key): void
    {
        $this->getFrontendUser()->setAndSaveSessionData($this->getSessionKey($key), false);
        $this->getDigit($key);
    }

    public function getDigit(int $key): int
    {
        $frontendUser = $this->getFrontendUser();
        $digit = (int) $this->stringify($frontendUser->getSessionData($this->getSessionKey($key)));
        if ($digit === 0) {
            $digit = random_int(1, 9);
            $frontendUser->setAndSaveSessionData($this->getSessionKey($key), $digit);
        }
        return $digit;
    }

    public function toHtml(): string
    {
        return $this->renderLabel() . $this->renderCaptchaInput();
    }

    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        $data['input'] = $this->renderCaptchaInput();
        $data['label'] = $this->renderLabel();
        $data['operation'] = $this->renderOperation();
        return $data;
    }

    protected function renderCaptchaInput(): string
    {
        return '<input type="text" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getEscapedValue() . '"'
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

    private function getSessionKey(int $key): string
    {
        return 'form-' . $this->form->getIdentifier() . '-' . $this->getHtmlId() . '-digit-' . $key;
    }

    private function getFrontendUser(): FrontendUserAuthentication
    {
        $frontendUser = $this->form->getCurrentRequest()->getAttribute('frontend.user');
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            throw new \RuntimeException('The numeric captcha requires a frontend request', 1731340808);
        }
        return $frontendUser;
    }
}
