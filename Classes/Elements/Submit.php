<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Submit extends ElementAbstract
{
    /**
     * @var bool $searchable searchable
     */
    protected $searchable = false;

    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string
    {
        if (isset($this->configuration['src'])) {
            return '<input type="image" '
                . 'src="' . $this->configuration['src'] . '" '
                . 'id="' . $this->getHtmlId() . '" '
                . 'value="' . $this->getLabel() . '" '
                . 'name="' . $this->absolutename . '"'
                . $this->getAttributes() . ' />';
        } else {
            return '<input type="submit" '
                . 'id="' . $this->getHtmlId() . '" '
                . 'value="' . $this->getLabel() . '" '
                . 'name="' . $this->absolutename . '"'
                . $this->getAttributes() . ' />';
        }
    }

    /**
     * return label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return isset($this->configuration['label']) ? $this->configuration['label'] : 'Envoyer';
    }

    /**
     * return true if must check constraints
     *
     * @return bool
     */
    public function checkConstraints(): bool
    {
        return isset($this->configuration['check_constraints']) ? $this->configuration['check_constraints'] : true;
    }

    /**
     * return true if the button is clicked
     *
     * @return bool
     */
    public function isClicked(): bool
    {
        if ($this->form->isSubmitted()) {
            $post = $this->form->getBodyData();
            return isset($post[$this->name]);
        }
        return false;
    }
}
