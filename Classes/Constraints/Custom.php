<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Exception\BadConfigurationException;

/**
 * Configuration: method, a callable fn(mixed $value, Form $form): bool
 */
class Custom extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        $method = $this->configuration['method'] ?? null;
        if (!is_callable($method)) {
            throw new BadConfigurationException('Custom constraint must have a callable method for validation', 1731340811);
        }
        return (bool) $method($value, $this->form);
    }
}
