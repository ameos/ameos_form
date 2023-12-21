<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Text extends ElementAbstract
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        return '<input type="text" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />' . $this->getDatalist();
    }
}
