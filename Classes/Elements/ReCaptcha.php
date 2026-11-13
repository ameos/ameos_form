<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Constraints\ReCaptcha as ReCaptchaConstraint;
use Ameos\AmeosForm\Form\Form;

/**
 * Google reCAPTCHA v2. Options: publicKey, privateKey, theme, type, size, tabindex, callback,
 * expired-callback, onload, render, language, errormessage
 */
class ReCaptcha extends ElementAbstract
{
    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        $query = http_build_query([
            'onload' => $this->stringify($this->configuration['onload'] ?? ''),
            'render' => $this->stringify($this->configuration['render'] ?? 'onload'),
            'hl' => $this->stringify($this->configuration['language'] ?? ''),
        ]);
        $this->assetCollector->addJavaScript('recaptcha', 'https://www.google.com/recaptcha/api.js?' . $query);

        $this->addConstraint(new ReCaptchaConstraint(
            $this->stringify($this->configuration['errormessage'] ?? 'ReCaptcha is not valid'),
            ['privateKey' => $this->stringify($this->configuration['privateKey'] ?? '')],
            $this,
            $form,
        ));
    }

    public function toHtml(): string
    {
        $dataAttributes = [
            'sitekey' => $this->configuration['publicKey'] ?? '',
            'theme' => $this->configuration['theme'] ?? 'light',
            'type' => $this->configuration['type'] ?? 'image',
            'size' => $this->configuration['size'] ?? 'normal',
            'tabindex' => $this->configuration['tabindex'] ?? '0',
            'callback' => $this->configuration['callback'] ?? '',
            'expired-callback' => $this->configuration['expired-callback'] ?? '',
        ];
        $attributes = '';
        foreach ($dataAttributes as $key => $value) {
            $attributes .= ' data-' . $key . '="' . $this->escapeConfiguration($value) . '"';
        }
        return '<div class="g-recaptcha"' . $attributes . '></div>';
    }
}
