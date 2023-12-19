<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Constraints\ReCaptcha as ReCaptchaConstraint;

class ReCaptcha extends ElementAbstract
{
    /**
     * @constuctor
     *
     * @param    string    $absolutename absolutename
     * @param    string    $name name
     * @param    array    $configuration configuration
     * @param    \Ameos\AmeosForm\Form $form form
     */
    public function __construct($absolutename, $name, $configuration, $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        $onload          = isset($configuration['onload']) ? $configuration['onload'] : '';
        $render          = isset($configuration['render']) ? $configuration['render'] : 'onload';
        $language        = isset($configuration['language']) ? $configuration['language'] : '';

        $this->pageRenderer->addJsFooterFile('https://www.google.com/recaptcha/api.js?onload=' . $onload . '&render=' . $render . '&hl=' . $language, 'text/javascript', false, false, '', true);

        $errorMessage = isset($configuration['errormessage']) ? $configuration['errormessage'] : 'ReCaptcha is not valid';
        $constraint = GeneralUtility::makeInstance(
            ReCaptchaConstraint::class,
            $errorMessage,
            ['privateKey' => $configuration['privateKey']],
            $this,
            $form
        );
        $this->addConstraint($constraint);
    }

    /**
     * form to html
     *
     * @return    string the html
     */
    public function toHtml()
    {
        $theme           = isset($this->configuration['theme']) ? $this->configuration['theme'] : 'light';
        $type            = isset($this->configuration['type']) ? $this->configuration['type'] : 'image';
        $size            = isset($this->configuration['size']) ? $this->configuration['size'] : 'normal';
        $tabindex        = isset($this->configuration['tabindex']) ? $this->configuration['tabindex'] : '0';
        $callback        = isset($this->configuration['callback']) ? $this->configuration['callback'] : '';
        $expiredcallback = isset($this->configuration['expired-callback']) ? $this->configuration['expired-callback'] : '';

        return '<div class="g-recaptcha" data-sitekey="' . $this->configuration['publicKey'] . '" data-theme="' . $theme . '" data-type="' . $type . '" data-size="' . $size . '" data-tabindex="' . $tabindex . '" data-callback="' . $callback . '" data-expired-callback="' . $expiredcallback . '"></div>';
    }
}
