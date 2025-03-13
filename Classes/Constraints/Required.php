<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Elements\Textdate;
use Ameos\AmeosForm\Elements\Upload;

class Required extends ConstraintAbstract
{
    /**
     * return true if the element is valide
     *
     * @param   null|string|array|\DateTime $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        $value = is_null($value) ? '' : $value;
        if (is_a($this->element, Upload::class)) {
            if (is_array($value) && array_key_exists('upload', $value) && is_array($value['upload'])) {
                return true;
            }
        }
        if (is_a($this->element, Textdate::class)) {
            return is_a($value, \DateTime::class);
        }
        if (is_array($value)) {
            return !empty($value);
        }
        return !(trim($value) == '');
    }
}
