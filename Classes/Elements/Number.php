<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Number extends ElementAbstract
{
    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        $additionnalAttributes = '';
        $additionnalAttributes .= $this->getAttribute('min');
        $additionnalAttributes .= $this->getAttribute('max');
        $additionnalAttributes .= $this->getAttribute('step');

        return '<input type="number" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getValue() . '"'
            . $this->getAttributes()
            . $additionnalAttributes . ' />';
    }

    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array
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
}
