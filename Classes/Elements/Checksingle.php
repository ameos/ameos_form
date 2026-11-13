<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Checksingle extends ElementAbstract
{
    public function isOmittedFromRequestWhenEmpty(): bool
    {
        return !$this->isDisabled();
    }

    public function toHtml(): string
    {
        $checked = $this->stringify($this->getValue()) === '1' ? ' checked="checked"' : '';
        return '<input type="checkbox"'
            . ' id="' . $this->getHtmlId() . '"'
            . ' name="' . $this->absolutename . '"'
            . ' value="1"'
            . $this->getAttributes()
            . $checked . ' />';
    }
}
