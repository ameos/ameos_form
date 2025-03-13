<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $attributes .= isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';
        $attributes .= isset($this->configuration['style']) ? ' style="' . $this->configuration['style'] . '"' : '';
        if (isset($this->configuration['disabled']) && $this->configuration['disabled'] == true) {
            $attributes .= ' disabled="disabled"';
        }
        $attributes .= isset($this->configuration['custom']) ? ' ' . $this->configuration['custom'] : '';

        $output = '';
        foreach ($this->configuration['items'] as $value => $label) {
            $checked = in_array($value, $this->getValue()) ? ' checked="checked"' : '';
            $output .= '
                <input id="' . $this->getHtmlId() . '-' . $value . '"'
                . ' name="' . $this->absolutename . '[]"'
                . ' type="checkbox"'
                . ' value="' . $value . '"'
                . '' . $checked . $attributes . ' />' .
                '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label><br />';
        }
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
        $data['items'] = [];
        foreach ($this->configuration['items'] as $value => $label) {
            $currentValue = $this->getValue();
            if ($currentValue === null) {
                $currentValue = $this->configuration['defaultValue'];
            }
            $checked = in_array($value, $currentValue) ? ' checked="checked"' : '';
            $data['items'][$value] = array(
                'input' => '<input id="' . $this->getHtmlId() . '-' . $value . '" '
                    . 'name="' . $this->absolutename . '[]" '
                    . 'type="checkbox" value="' . $value . '"' . $checked . ' />',
                'label' => '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>',
            );
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
