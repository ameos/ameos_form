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

class Range extends ElementAbstract
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        $additionnalAttributes = '';
        if (isset($this->configuration['min'])) {
            $additionnalAttributes .= ' min="' . $this->configuration['min'] . '"';
        }
        if (isset($this->configuration['max'])) {
            $additionnalAttributes .= ' max="' . $this->configuration['max'] . '"';
        }
        if (isset($this->configuration['step'])) {
            $additionnalAttributes .= ' step="' . $this->configuration['step'] . '"';
        }

        return '<input type="range" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . $additionnalAttributes . ' />' . $this->getDatalist();
    }

    /**
     * return rendering information
     *
     * @return  array rendering information
     */
    public function getRenderingInformation()
    {
        $data = parent::getRenderingInformation();

        if (isset($this->configuration['min'])) {
            $data['min']  = $this->configuration['min'];
        }
        if (isset($this->configuration['max'])) {
            $data['max']  = $this->configuration['max'];
        }
        if (isset($this->configuration['step'])) {
            $data['step'] = $this->configuration['step'];
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
