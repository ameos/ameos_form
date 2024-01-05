<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

class Sameas extends ConstraintAbstract
{
    /**
     * return true if the element is sameas an another value
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        return $value == $this->form->getElement($this->configuration['sameas'])->getValue();
    }
}
