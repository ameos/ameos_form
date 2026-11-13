<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Textarea extends ElementAbstract
{
    public function toHtml(): string
    {
        return '<textarea id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '"'
            . $this->getAttributes() . '>'
            . $this->getEscapedValue()
            . '</textarea>';
    }
}
