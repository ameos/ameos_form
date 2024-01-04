<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Textarea extends ElementAbstract
{
    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        return '<textarea id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' 
            . $this->getAttributes() . '>' . $this->getValue() . '</textarea>';
    }
}
