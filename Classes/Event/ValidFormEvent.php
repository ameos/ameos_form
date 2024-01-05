<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Event;

use Ameos\AmeosForm\Form\Form;

final class ValidFormEvent
{
    /**
     * construct
     *
     * @param Form $form
     */
    public function __construct(private readonly Form $form)
    {
        
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
}
