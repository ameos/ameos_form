<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;

class Password extends ElementAbstract
{
    protected bool $searchable = false;

    /**
     * Options: encrypt (default true), fill_value (default false), fill_value_iferror (default true)
     *
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        $configuration ??= [];
        $configuration['encrypt'] = (bool) ($configuration['encrypt'] ?? true);
        $configuration['fill_value'] = (bool) ($configuration['fill_value'] ?? false);
        $configuration['fill_value_iferror'] = (bool) ($configuration['fill_value_iferror'] ?? true);
        parent::__construct($absolutename, $name, $configuration, $form);
    }

    public function toHtml(): string
    {
        $attributes = $this->getAttributes();
        if ($this->shouldFillValue()) {
            $attributes .= ' value="' . $this->getEscapedValue() . '"';
        }
        return '<input type="password" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '"'
            . $attributes . ' />';
    }

    private function shouldFillValue(): bool
    {
        if ($this->configuration['fill_value'] === true) {
            return true;
        }
        return $this->configuration['fill_value_iferror'] === true
            && $this->form->isSubmitted()
            && !$this->form->isValid();
    }
}
