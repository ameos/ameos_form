<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Tel extends ElementAbstract
{
    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        $pattern = isset($this->configuration['pattern']) ? ' pattern="' . $this->configuration['pattern'] . '"' : '';

        return '<input type="tel" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" '
            . 'value="' . $this->getValue() . '"' . $this->getAttributes() . $pattern . ' />' . $this->getDatalist();
    }
}
