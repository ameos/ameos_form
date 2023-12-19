<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

class Filesize extends ConstraintAbstract
{
    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        if (!is_array($value) && empty($value)) {
            return true;
        }

        if (is_array($value)) {
            $maxsize = (int)$this->configuration['maxsize'];
            if (
                strtoupper(substr($this->configuration['maxsize'], -1)) == 'K'
                || strtoupper(substr($this->configuration['maxsize'], -2)) == 'KO'
            ) {
                $maxsize = $maxsize * 1024;
            }
            if (
                strtoupper(substr($this->configuration['maxsize'], -1)) == 'M'
                || strtoupper(substr($this->configuration['maxsize'], -2)) == 'MO'
            ) {
                $maxsize = $maxsize * 1024 * 1024;
            }
            if (
                strtoupper(substr($this->configuration['maxsize'], -1)) == 'G'
                || strtoupper(substr($this->configuration['maxsize'], -2)) == 'GO'
            ) {
                $maxsize = $maxsize * 1024 * 1024 * 1024;
            }

            return (int)$value['size'] <= $maxsize;
        }
        return false;
    }
}
