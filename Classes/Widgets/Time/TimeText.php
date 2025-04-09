<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Time;

use Ameos\AmeosForm\Widgets\WidgetAbstract;
use Ameos\AmeosForm\Widgets\WidgetInterface;

class TimeText extends WidgetAbstract implements WidgetInterface
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml(): string
    {
        return '<input type="time" '
            . 'id="' . $this->element->getHtmlId() . '" '
            . 'name="' . $this->element->getAbsoluteName() . '" '
            . 'value="' . $this->element->getValue() . '"'
            . $this->element->getAttributes() . ' />';
    }
}
