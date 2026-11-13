<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Date;

use Ameos\AmeosForm\Widgets\WidgetAbstract;

final class DateText extends WidgetAbstract
{
    use DateValue;

    public function toHtml(): string
    {
        return '<input type="date" '
            . 'id="' . $this->element->getHtmlId() . '" '
            . 'name="' . $this->element->getAbsoluteName() . '" '
            . 'value="' . ($this->getDateValueOrNull()?->format('Y-m-d') ?? '') . '"'
            . $this->element->getAttributes() . ' />'
            . $this->element->getDatalist();
    }
}
