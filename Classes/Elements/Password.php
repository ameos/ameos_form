<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;

class Password extends ElementAbstract
{
    /**
     * @var bool $searchable searchable
     */
    protected $searchable = false;

    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        $configuration['encrypt'] = isset($configuration['encrypt']) ? (bool)$configuration['encrypt'] : true;
        $configuration['fill_value'] = isset($configuration['fill_value'])
            ? (bool)$configuration['fill_value']
            : false;
        $configuration['fill_value_iferror'] = isset($configuration['fill_value_iferror'])
            ? (bool)$configuration['fill_value_iferror']
            : true;

        parent::__construct($absolutename, $name, $configuration, $form);
    }

    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        $attributes = $this->getAttributes();
        if (
            $this->configuration['fill_value']
            || ($this->form->isSubmitted() && !$this->form->isValid() && $this->configuration['fill_value_iferror'])
        ) {
            $attributes .= ' value="' . $this->getValue() . '"';
        }
        return '<input type="password" id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '"' . $attributes . ' />';
    }
}
