<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Elements\Traits\NumericBounds;

class Number extends ElementAbstract
{
    use NumericBounds;

    public function toHtml(): string
    {
        return '<input type="number" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getEscapedValue() . '"'
            . $this->getAttributes()
            . $this->getBoundsAttributes() . ' />';
    }

    public function getRenderingInformation(): array
    {
        return $this->addBoundsRenderingInformation(parent::getRenderingInformation());
    }
}
