<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Url extends ElementAbstract
{
    public function toHtml(): string
    {
        return '<input type="url" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getEscapedValue() . '"'
            . $this->getAttributes() . ' />'
            . $this->getDatalist();
    }
}
