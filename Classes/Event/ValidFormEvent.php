<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Event;

use Ameos\AmeosForm\Form\Form;

/**
 * Dispatched once, the first time a submitted form is found valid.
 */
final readonly class ValidFormEvent
{
    public function __construct(private Form $form) {}

    public function getForm(): Form
    {
        return $this->form;
    }
}
