<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Radio extends ElementAbstract
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
        $attributes .= isset($this->configuration['disabled']) && $this->configuration['disabled'] == true ? ' disabled="disabled"' : '';
        $attributes .= isset($this->configuration['custom']) ? ' ' . $this->configuration['custom'] : '';

        $output = '';
        foreach ($this->configuration['items'] as $value => $label) {
            $checked = ($this->getValue() == $value) ? ' checked="checked"' : '';
            $output .= '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '" type="radio" value="' . $value . '"' . $checked . $attributes . ' />' .
                '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>';
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
            $checked = ($this->getValue() == $value) ? ' checked="checked"' : '';
            $data['items'][$value] = array(
                'input' => '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '" type="radio" value="' . $value . '"' . $checked . ' />',
                'label' => '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>',
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
