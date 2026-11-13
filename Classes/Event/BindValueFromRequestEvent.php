<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Event;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

/**
 * Dispatched before a value from the request is bound to an element. Listeners may change the value.
 */
final class BindValueFromRequestEvent
{
    public function __construct(
        private readonly Form $form,
        private readonly ElementInterface $element,
        private mixed $value,
    ) {}

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getElement(): ElementInterface
    {
        return $this->element;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }
}
