<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

interface ConstraintInterface
{
    public function isValid(mixed $value): bool;

    /**
     * Error message, NULL if the constraint adds its own messages to the error manager
     */
    public function getMessage(): ?string;
}
