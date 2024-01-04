<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Color extends ElementAbstract
{
    /**
     * form to html
     *
     * @return string the html
     */
    public function toHtml(): string
    {
        return
            '<input type="color"'
            . ' id="' . $this->getHtmlId() . '"'
            . ' name="' . $this->absolutename . '"'
            . ' value="' . $this->getValue() . '"'
            . $this->getAttributes() . ' />';
    }
}
