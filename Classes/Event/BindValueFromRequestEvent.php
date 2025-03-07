<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Event;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

final class BindValueFromRequestEvent
{
    /**
     * construct
     *
     * @param Form $form
     */
    public function __construct(
        private readonly Form $form,
        private readonly ElementInterface $element,
        private mixed $value
    ) {
    }

    /**
     * return form
     *
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * return element
     *
     * @return ElementInterface
     */
    public function getElement(): ElementInterface
    {
        return $this->element;
    }

    /**
     * return value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * set value
     *
     * @param mixed $value
     * @return self
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }
}
