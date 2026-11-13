<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Time;

use Ameos\AmeosForm\Widgets\WidgetAbstract;

final class TimeText extends WidgetAbstract
{
    public function toHtml(): string
    {
        $value = $this->element->getValue();
        return '<input type="time" '
            . 'id="' . $this->element->getHtmlId() . '" '
            . 'name="' . $this->element->getAbsoluteName() . '" '
            . 'value="' . $this->escape(is_string($value) ? $value : '') . '"'
            . $this->element->getAttributes() . ' />';
    }
}
