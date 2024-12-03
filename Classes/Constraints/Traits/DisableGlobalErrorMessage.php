<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints\Traits;

trait DisableGlobalErrorMessage
{
    function getMessage()
    {
        return null;
    }
}