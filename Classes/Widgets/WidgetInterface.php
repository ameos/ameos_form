<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

interface WidgetInterface
{
    public function __construct(ElementInterface $element, Form $form);

    /**
     * @param array<string, mixed> $dataFromElement
     * @return array<string, mixed>
     */
    public function getRenderingInformation(array $dataFromElement): array;

    public function toHtml(): string;
}
