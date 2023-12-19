<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Url extends ElementAbstract
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        return '<input type="url" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />' . $this->getDatalist();
    }
}
