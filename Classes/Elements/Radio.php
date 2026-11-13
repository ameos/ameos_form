<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Elements\Traits\Choices;

class Radio extends ElementAbstract
{
    use Choices;

    public function getClause(): array|false
    {
        $value = $this->getValue();
        if ($value === '' || $value === null) {
            return false;
        }
        return $this->overrideClause !== null ? $this->buildOverriddenClause() : $this->buildClause('equals', $value);
    }

    protected function renderSingleInput(string|int $value, string $label): string
    {
        $checked = $this->stringify($this->getValue()) === (string) $value ? ' checked="checked"' : '';
        return '<input '
            . 'id="' . $this->getHtmlId() . '-' . $this->escapeConfiguration($value) . '" '
            . 'name="' . $this->absolutename . '" '
            . 'type="radio" '
            . 'value="' . $this->escapeConfiguration($value) . '"'
            . $checked
            . $this->getChoiceAttributes() . ' />';
    }

    protected function getNoItemsErrorMessage(): string
    {
        return 'No items defined for this radio button';
    }
}
