<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints\Traits;

/**
 * For constraints adding their own (per file) messages to the error manager
 */
trait DisableGlobalErrorMessage
{
    public function getMessage(): ?string
    {
        return null;
    }
}
