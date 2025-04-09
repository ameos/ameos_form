<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Button extends ElementAbstract
{
    public const TYPE_BUTTON = 'button';
    public const TYPE_SUBMIT = 'submit';

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
        $label = isset($this->configuration['label']) ? $this->configuration['label'] : 'Envoyer';
        return '<button id="' . $this->getHtmlId() . '"'
            . ' name="' . $this->absolutename . '"'
            . $this->getAttributes()
            . '>' . $this->getLabel() . '</button>';
    }

    /**
     * return type
     *
     * @return string
     */
    public function getType(): string
    {
        $configuration = $this->getConfiguration();
        if (array_key_exists('type', $configuration)) {
            return $configuration['type'];
        } else {
            return self::TYPE_BUTTON;
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
