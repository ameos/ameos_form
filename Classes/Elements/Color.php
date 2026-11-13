<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Color extends ElementAbstract
{
    public function toHtml(): string
    {
        return '<input type="color" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getEscapedValue() . '"'
            . $this->getAttributes() . ' />';
    }
}
