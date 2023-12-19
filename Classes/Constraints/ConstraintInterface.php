<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

interface ConstraintInterface
{
    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value);

    /**
     * return the message
     *
     * @return  string the message
     */
    public function getMessage();
}
