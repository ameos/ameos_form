<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Elements\Traits\Submitter;

class Button extends ElementAbstract
{
    use Submitter;

    public const string TYPE_BUTTON = 'button';
    public const string TYPE_SUBMIT = 'submit';

    protected bool $searchable = false;

    public function toHtml(): string
    {
        return '<button id="' . $this->getHtmlId() . '"'
            . ' name="' . $this->absolutename . '"'
            . $this->getAttributes()
            . '>' . $this->renderLabelContent($this->getLabel()) . '</button>';
    }

    public function getType(): string
    {
        $type = $this->configuration['type'] ?? null;
        return is_string($type) ? $type : self::TYPE_BUTTON;
    }
}
