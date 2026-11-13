<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements\Traits;

/**
 * Rendering shared by checkbox and radio elements (one input + label per item)
 *
 * @phpstan-require-extends \Ameos\AmeosForm\Elements\ElementAbstract
 */
trait Choices
{
    abstract protected function renderSingleInput(string|int $value, string $label): string;

    abstract protected function getNoItemsErrorMessage(): string;

    public function toHtml(): string
    {
        $output = '';
        $separator = $this->stringify($this->getConfigurationItem('separator'));
        foreach ($this->getItems($this->getNoItemsErrorMessage()) as $value => $label) {
            $output .= $this->renderSingleInput($value, $this->stringify($label))
                . $this->renderSingleLabel($value, $this->stringify($label))
                . $separator;
        }
        return $output;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        $data['choices'] = [];
        foreach ($this->getItems($this->getNoItemsErrorMessage()) as $value => $label) {
            $data['choices'][$value] = [
                'input' => $this->renderSingleInput($value, $this->stringify($label)),
                'label' => $this->renderSingleLabel($value, $this->stringify($label)),
            ];
        }
        return $data;
    }

    /**
     * Label is escaped, unless the "htmllabel" option is set (e.g. a link to terms and conditions).
     * Never enable it for labels coming from records or user input.
     */
    protected function renderSingleLabel(string|int $value, string $label): string
    {
        return '<label for="' . $this->getHtmlId() . '-' . $this->escapeConfiguration($value) . '">'
            . $this->renderLabelContent($label) . '</label>';
    }

    protected function getChoiceAttributes(): string
    {
        return $this->getClassAttribute()
            . $this->getAttribute('style')
            . $this->getAttribute('disabled', 'bool')
            . $this->getCustomAttribute();
    }
}
