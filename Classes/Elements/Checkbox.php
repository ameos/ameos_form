<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;

class Checkbox extends ElementAbstract
{
    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        $attributes = '';
        $attributes .= $this->getClassAttribute();
        $attributes .= $this->getAttribute('style');
        $attributes .= $this->getAttribute('disabled', 'bool');
        $attributes .= $this->getCustomAttribute();

        if (!isset($this->configuration['items'])) {
            throw new BadConstraintException('No items defined for this checkbox');
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
        $checked = in_array($value, $currentValue) ? ' checked="checked"' : '';
        return  '<input '
            . 'id="' . $this->getHtmlId() . '-' . $value . '" '
            . 'name="' . $this->absolutename . '[]" '
            . 'type="checkbox" '
            . 'value="' . $value . '"'
            . $checked . ' />';
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
            throw new BadConstraintException('No items defined for this checkbox');
        }

        foreach ($this->configuration['items'] as $value => $label) {
            $data['choices'][$value] = [
                'input' => $this->renderSingleInput($value, $label),
                'label' => $this->renderSingleLabel($value, $label),
            ];
        }
        return $data;
    }

    /**
     * set the value
     *
     * @param mixed $value value
     * @return self
     */
    public function setValue(mixed $value): self
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        parent::setValue($value);
        return $this;
    }

    /**
     * return the value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        $value = parent::getValue();
        if (!is_array($value)) {
            $value = GeneralUtility::trimExplode(',', $value);
        }

        return $value;
    }

    /**
     * return where clause
     *
     * @return array|false
     */
    public function getClause(): array|false
    {
        $values = $this->getValue();
        if (!empty($values)) {
            if ($this->overrideClause !== false) {
                return parent::getClause();
            } else {
                $clauses = [];
                if (is_array($values)) {
                    foreach ($values as $value) {
                        $clauses[] = [
                            'field' => $this->getSearchField(),
                            'type'  => 'contains',
                            'value' => $value,
                        ];
                    }
                }
                return [
                    'elementname'  => $this->getName(),
                    'elementvalue' => $this->getValue(),
                    'field'   => $this->getSearchField(),
                    'type'    => 'logicalOr',
                    'clauses' => $clauses,
                ];
            }
        }
        return false;
    }
}
