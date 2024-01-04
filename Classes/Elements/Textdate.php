<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Textdate extends ElementAbstract
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        return '<input type="date" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValueFormatted() . '"' . $this->getAttributes() . ' />' . $this->getDatalist();
    }

    /**
     * return timestamp value
     *
     * @return \Ameos\AmeosForm\Elements\Textdate this
     */
    public function setValue($value): Textdate
    {
        // todo convert datetime ?
        parent::setValue($value);
        return $this;
    }

    /**
     * return formatted value
     *
     * @return  string the html
     */
    protected function getValueFormatted()
    {
        if ($this->value) {
            return $this->value->format('Y-m-d');
        }
    }
}
