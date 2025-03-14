<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

abstract class WidgetAbstract
{
    /**
     * @param ElementInterface $element
     * @param Form $form
     */
    public function __construct(protected ElementInterface $element, protected Form $form)
    {
    }

    /**
     * @param array $dataFromElement
     * @return array
     */
    public function getRenderingInformation(array $dataFromElement): array
    {
        return $dataFromElement;
    }

    /**
     * @return string
     */
    abstract public function toHtml(): string;
}
