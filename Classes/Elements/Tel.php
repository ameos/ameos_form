<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Tel extends ElementAbstract
{
    public function toHtml(): string
    {
        return '<input type="tel" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getEscapedValue() . '"'
            . $this->getAttributes()
            . $this->getAttribute('pattern') . ' />'
            . $this->getDatalist();
    }
}
