<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Dropdown extends ElementAbstract
{
    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (!isset($this->configuration['optionValueField'])) {
            $this->configuration['optionValueField'] = 'uid';
        }
    }

    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        if ($this->isMultiple()) {
            $output = '<select id="' . $this->getHtmlId() . '" '
                . 'name="' . $this->absolutename . '[]"' . $this->getAttributes() . '>';
        } else {
            $output = '<select id="' . $this->getHtmlId() . '" '
                . 'name="' . $this->absolutename . '"' . $this->getAttributes() . '>';
        }

        if (isset($this->configuration['placeholder'])) {
            $output .= '<option value="">' . $this->configuration['placeholder'] . '</option>';
        }

        $currentValue = $this->getValue();
        if (!is_array($currentValue)) {
            $currentValue = [$currentValue];
        }

        if (!isset($this->configuration['items'])) {
            throw new BadConstraintException('No items defined for this dropdown');
        }

        if (is_array($this->configuration['items'])) {
            $optionValueFieldMethod = 'get' . ucfirst($this->configuration['optionValueField']);
            foreach ($currentValue as $currentValueKey => $currentValueItem) {
                if (is_a($currentValueItem, AbstractEntity::class)) {
                    $currentValue[$currentValueKey] = $currentValueItem->$optionValueFieldMethod();
                } elseif (is_a($currentValueItem, ObjectStorage::class)) {
                    foreach ($currentValueItem as $objects) {
                        $currentValue[$currentValueKey] = $objects->$optionValueFieldMethod();
                    }
                }
            }
            foreach ($this->configuration['items'] as $value => $label) {
                $selected = in_array($value, $currentValue) ? ' selected="selected"' : '';
                $output .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
            }
        } elseif (is_object($this->configuration['items']) && is_a($this->configuration['items'], QueryResult::class)) {
            $optionLabelFieldMethod = 'get' . ucfirst($this->configuration['optionLabelField']);
            $optionValueFieldMethod = 'get' . ucfirst($this->configuration['optionValueField']);

            foreach ($currentValue as $key => $value) {
                if (is_object($value) && is_a($value, AbstractEntity::class)) {
                    $currentValue[$key] = $this->getValue()->$optionValueFieldMethod();
                } elseif (is_object($value) && is_a($value, ObjectStorage::class)) {
                    $currentValue = [];
                    foreach ($value as $subkey => $subvalue) {
                        if (is_object($subvalue) && is_a($subvalue, AbstractEntity::class)) {
                            $currentValue[] = $subvalue->$optionValueFieldMethod();
                        }
                    }
                }
            }

            foreach ($this->configuration['items'] as $model) {
                $selected = in_array($model->$optionValueFieldMethod(), $currentValue) ? ' selected="selected"' : '';
                $output .= '<option value="' . $model->$optionValueFieldMethod() . '"' . $selected . '>'
                    . $model->$optionLabelFieldMethod() . '</option>';
            }
        }
        $output .= '</select>';
        return $output;
    }


    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        if (isset($this->configuration['items']) && is_array($this->configuration['items'])) {
            $data['choices'] = $this->configuration['items'];
        }

        return $data;
    }

    /**
     * return html attribute
     *
     * @return string
     */
    public function getAttributes(): string
    {
        $output = parent::getAttributes();
        $output .= $this->getAttribute('multiple', 'bool');
        return $output;
    }

    /**
     * return true if it's a multiple dropdown
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return isset($this->configuration['multiple']) && $this->configuration['multiple'] == true;
    }

    /**
     * return where clause
     *
     * @return array|false
     */
    public function getClause(): array|false
    {
        $value = $this->getValue();
        if (!empty($value)) {
            if ($this->overrideClause !== false) {
                return parent::getClause();
            } else {
                if ($this->isMultiple()) {
                    return [
                        'elementname'  => $this->getName(),
                        'elementvalue' => $this->getValue(),
                        'field' => $this->getSearchField(),
                        'type'  => 'in',
                        'value' => $value
                    ];
                } else {
                    return [
                        'elementname'  => $this->getName(),
                        'elementvalue' => $this->getValue(),
                        'field' => $this->getSearchField(),
                        'type'  => 'equals',
                        'value' => $value
                    ];
                }
            }
        }
        return false;
    }
}
