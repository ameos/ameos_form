<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Date;

use Ameos\AmeosForm\Widgets\WidgetAbstract;
use Ameos\AmeosForm\Widgets\WidgetInterface;

class DateText extends WidgetAbstract implements WidgetInterface
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml(): string
    {
        $value = $this->element->getValue();
        if ($value === '') {
            $value = new \DateTime();
        } elseif (is_numeric($value)) {
            $value = new \DateTime('@' . $value);
        }

        return '<input type="date" id="' . $this->element->getHtmlId()
            . '" name="' . $this->element->getAbsoluteName()
            . '" value="' . $value->format('Y-m-d') . '"'
            . '"' . $this->element->getAttributes() . ' />'
            . $this->element->getDatalist();
    }
}
