<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;

class Radio extends ElementAbstract
{
    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        if (!isset($this->configuration['items'])) {
            throw new BadConstraintException('No items defined for this radio button');
        }

        $output = '';
        $separator = $this->getConfigurationItem('separator') ?? '';
        foreach ($this->configuration['items'] as $value => $label) {
            $output .= $this->renderSingleInput($value, $label) . $this->renderSingleLabel($value, $label) . $separator;
        }
        return $output;
    }

    protected function renderSingleInput(string|int $value, string $label): string
    {
        $currentValue = $this->getValue();
        if ($currentValue === null) {
            $currentValue = $this->configuration['defaultValue'];
        }
        $checked = ($currentValue == $value) ? ' checked="checked"' : '';

        $attributes = '';
        $attributes .= $this->getClassAttribute();
        $attributes .= $this->getAttribute('style');
        $attributes .= $this->getAttribute('disabled', 'bool');
        $attributes .= $this->getCustomAttribute();

        return  '<input '
            . 'id="' . $this->getHtmlId() . '-' . $value . '" '
            . 'name="' . $this->absolutename . '" '
            . 'type="radio" '
            . 'value="' . $value . '"'
            . $checked
            . $attributes . ' />';
    }

    protected function renderSingleLabel(string|int $value, string $label): string
    {
        return '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>';
    }

    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        $data['choices'] = [];

        if (!isset($this->configuration['items'])) {
            throw new BadConstraintException('No items defined for this radio button');
        }

        foreach ($this->configuration['items'] as $value => $label) {
            $data['choices'][$value] = array(
                'input' => $this->renderSingleInput($value, $label),
                'label' => $this->renderSingleLabel($value, $label),
            );
        }
        return $data;
    }

    /**
     * return where clause
     *
     * @return array|false
     */
    public function getClause(): array|false
    {
        if ($this->getValue() != '') {
            if ($this->overrideClause !== false) {
                return parent::getClause();
            } else {
                return [
                    'elementname'  => $this->getName(),
                    'elementvalue' => $this->getValue(),
                    'field' => $this->getSearchField(),
                    'type'  => 'equals',
                    'value' => $this->getValue()
                ];
            }
        }
        return false;
    }
}
