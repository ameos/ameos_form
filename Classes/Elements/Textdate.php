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
        $this->valueSetted = true;
        if (!is_a($value, \DateTime::class)) {
            if ($value != '') {
                $this->value = new \DateTime($value);
            } else {
                $this->value = '';
            }
        } else {
            $this->value = $value;
        }
        if ($this->form !== false) {
            if ($this->form->getMode() == 'crud/extbase') {
                $method = 'set' . \Ameos\AmeosForm\Utility\StringUtility::camelCase($this->name);
                if (method_exists($this->form->getModel(), $method) && is_a($this->value, \DateTime::class)) {
                    $this->form->getModel()->$method($this->value);
                }
            }

            if ($this->form->getMode() == 'crud/classic') {
                $this->form->setData($this->name, $this->value);
            }
        }
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
