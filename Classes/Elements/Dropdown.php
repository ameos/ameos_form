<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;

/**
 * Options: items (array value => label, or iterable of models with optionValueField / optionLabelField),
 * multiple, placeholder
 */
class Dropdown extends ElementAbstract
{
    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (!isset($this->configuration['optionValueField'])) {
            $this->configuration['optionValueField'] = 'uid';
        }
    }

    public function toHtml(): string
    {
        $output = '<select id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . ($this->isMultiple() ? '[]' : '') . '"'
            . $this->getAttributes() . '>';

        if (isset($this->configuration['placeholder'])) {
            $output .= '<option value="">' . $this->escapeConfiguration($this->configuration['placeholder']) . '</option>';
        }

        $selectedValues = $this->getSelectedValues();
        foreach ($this->getOptions() as $value => $label) {
            $selected = in_array((string) $value, $selectedValues, true) ? ' selected="selected"' : '';
            $output .= '<option value="' . $this->escapeConfiguration($value) . '"' . $selected . '>'
                . $this->escapeConfiguration($label) . '</option>';
        }

        return $output . '</select>';
    }

    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        if (is_array($this->configuration['items'] ?? null)) {
            $data['choices'] = $this->configuration['items'];
        }
        return $data;
    }

    /**
     * "placeholder" is rendered as first empty option, it is not a valid attribute of select
     */
    public function getAttributes(): string
    {
        $placeholder = $this->configuration['placeholder'] ?? null;
        unset($this->configuration['placeholder']);
        try {
            return parent::getAttributes() . $this->getAttribute('multiple', 'bool');
        } finally {
            if ($placeholder !== null) {
                $this->configuration['placeholder'] = $placeholder;
            }
        }
    }

    /**
     * A multiple select without selected option is not sent by the browser
     */
    public function isOmittedFromRequestWhenEmpty(): bool
    {
        return $this->isMultiple() && !$this->isDisabled();
    }

    public function isMultiple(): bool
    {
        return (bool) ($this->configuration['multiple'] ?? false);
    }

    public function getClause(): array|false
    {
        $value = $this->getValue();
        if ($this->isEmptyValue($value)) {
            return false;
        }
        if ($this->overrideClause !== null) {
            return $this->buildOverriddenClause();
        }
        return $this->buildClause($this->isMultiple() ? 'in' : 'equals', $value);
    }

    /**
     * @return array<array-key, string> option value => label
     */
    protected function getOptions(): array
    {
        $items = $this->configuration['items'] ?? null;
        if (is_array($items)) {
            return array_map($this->stringify(...), $items);
        }
        if (!is_iterable($items)) {
            throw new BadConstraintException('No items defined for this dropdown', 1731340805);
        }

        $valueField = $this->getOptionField('optionValueField');
        $labelField = $this->getOptionField('optionLabelField');
        $options = [];
        foreach ($items as $model) {
            $options[$this->stringify($this->readProperty($model, $valueField))] = $this->stringify(
                $this->readProperty($model, $labelField),
            );
        }
        return $options;
    }

    /**
     * Current value(s) as strings; entities and object storages are converted with the option value field
     *
     * @return list<string>
     */
    protected function getSelectedValues(): array
    {
        $value = $this->getValue();
        $values = is_iterable($value) ? $value : [$value];
        $valueField = $this->getOptionField('optionValueField');

        $selectedValues = [];
        foreach ($values as $item) {
            $selectedValues[] = $item instanceof AbstractEntity
                ? $this->stringify($this->readProperty($item, $valueField))
                : $this->stringify($item);
        }
        return $selectedValues;
    }

    private function getOptionField(string $key): string
    {
        return $this->stringify($this->configuration[$key] ?? 'uid');
    }
}
