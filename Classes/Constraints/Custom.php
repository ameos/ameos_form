<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

class Custom extends ConstraintAbstract
{
    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        if (!isset($this->configuration['method']) && !is_callable($this->configuration['method'])) {
            throw new \Exception('Custom constraint must have a callable method for validation');
        }
        return $this->configuration['method']($value, $this->form);
    }
}
