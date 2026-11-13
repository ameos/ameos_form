<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Elements\Traits\NumericBounds;

class Range extends ElementAbstract
{
    use NumericBounds;

    public function toHtml(): string
    {
        return '<input type="range" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $this->getEscapedValue() . '"'
            . $this->getAttributes()
            . $this->getBoundsAttributes() . ' />'
            . $this->getDatalist();
    }

    public function getRenderingInformation(): array
    {
        return $this->addBoundsRenderingInformation(parent::getRenderingInformation());
    }

    public function getClause(): array|false
    {
        $value = $this->getValue();
        if ($value === '' || $value === null) {
            return false;
        }
        return $this->overrideClause !== null ? $this->buildOverriddenClause() : $this->buildClause('equals', $value);
    }
}
