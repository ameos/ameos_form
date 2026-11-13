<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Elements\Traits\Submitter;

class Submit extends ElementAbstract
{
    use Submitter;

    protected bool $searchable = false;

    public function toHtml(): string
    {
        $source = $this->getAttribute('src');
        return '<input type="' . ($source !== '' ? 'image' : 'submit') . '" '
            . ($source !== '' ? ltrim($source) . ' ' : '')
            . 'id="' . $this->getHtmlId() . '" '
            . 'value="' . $this->escapeConfiguration($this->getLabel()) . '" '
            . 'name="' . $this->absolutename . '"'
            . $this->getAttributes() . ' />';
    }
}
