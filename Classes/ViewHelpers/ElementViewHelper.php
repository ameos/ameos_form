<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\ViewHelpers;

use Ameos\AmeosForm\Elements\ElementInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Render an element, optionally overriding some of its configuration:
 *
 * <form:element element="{myElement}" class="form-control" errorClass="is-invalid" />
 */
final class ElementViewHelper extends AbstractViewHelper
{
    /**
     * ViewHelper argument => element configuration key
     */
    private const array CONFIGURATION_ARGUMENTS = [
        'class' => 'class',
        'errorClass' => 'errorclass',
        'placeholder' => 'placeholder',
        'style' => 'style',
        'custom' => 'custom',
    ];

    protected $escapeChildren = false;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('element', ElementInterface::class, 'form element', false);
        $this->registerArgument('class', 'string', 'class attribute', false);
        $this->registerArgument('errorClass', 'string', 'class attribute added when the element has errors', false);
        $this->registerArgument('placeholder', 'string', 'placeholder attribute', false);
        $this->registerArgument('style', 'string', 'style attribute', false);
        $this->registerArgument('custom', 'string', 'custom attribute(s), output unescaped', false);
    }

    public function render(): string
    {
        $element = $this->arguments['element'];
        if (!$element instanceof ElementInterface) {
            return '';
        }

        foreach (self::CONFIGURATION_ARGUMENTS as $argument => $configurationKey) {
            $value = $this->arguments[$argument] ?? null;
            if (is_string($value) && $value !== '') {
                $element->addConfiguration($configurationKey, $value);
            }
        }

        return $element->toHtml();
    }
}
