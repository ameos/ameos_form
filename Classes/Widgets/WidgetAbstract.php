<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

abstract class WidgetAbstract implements WidgetInterface
{
    public function __construct(protected ElementInterface $element, protected Form $form) {}

    public function getRenderingInformation(array $dataFromElement): array
    {
        return $dataFromElement;
    }

    abstract public function toHtml(): string;

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
