<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Label extends ElementAbstract
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        return $this->getValue();
    }
}
