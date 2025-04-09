<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

interface WidgetInterface
{
    /**
     * @param ElementInterface $element
     * @param Form $form
     */
    public function __construct(ElementInterface $element, Form $form);

    /**
     * @param array $dataFromElement
     * @return array
     */
    public function getRenderingInformation(array $dataFromElement): array;

    /**
     * @return string
     */
    public function toHtml(): string;
}
