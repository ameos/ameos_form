<?php

namespace Ameos\AmeosForm\Elements;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class Radio extends ElementAbstract
{
    
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
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
     * @return  array rendering information
     */
    public function getRenderingInformation()
    {
        $data = parent::getRenderingInformation();
        $data['items'] = [];
        foreach ($this->configuration['items'] as $value => $label) {
            $checked = ($this->getValue() == $value) ? ' checked="checked"' : '';
            $data['items'][$value] = array(
                'input' => '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '" type="radio" value="' . $value . '"' . $checked . $class . ' />',
                'label' => '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>',
            );
        }
        return $data;
    }
    
    /**
     * return where clause
     *
     * @return  bool|array FALSE if no search. Else array with search type and value
     */
    public function getClause()
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
