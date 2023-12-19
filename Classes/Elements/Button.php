<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Button extends ElementAbstract
{
    /**
     * @var bool $searchable searchable
     */
    protected $searchable = false;

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        $label = isset($this->configuration['label']) ? $this->configuration['label'] : 'Envoyer';
        return '<button id="' . $this->getHtmlId() . '"'
            . ' name="' . $this->absolutename . '"'
            . $this->getAttributes()
            . '>' . $this->getLabel() . '</button>';
    }

    /**
     * return label
     * @return string the label
     */
    public function getLabel()
    {
        return isset($this->configuration['label']) ? $this->configuration['label'] : 'Envoyer';
    }

    /**
     * return type
     * @return string the type
     */
    public function getType()
    {
        $configuration = $this->getConfiguration();
        if (is_array($configuration) && array_key_exists('type', $configuration)) {
            return $configuration['type'];
        } else {
            return 'button';
        }
    }

    /**
     * return true if the button is clicked
     * @return bool
     */
    public function isClicked()
    {
        if ($this->form->isSubmitted()) {
            $post = GeneralUtility::_POST($this->form->getIdentifier());
            return isset($post[$this->getName()]);
        }
        return false;
    }

    /**
     * return true if must check constraints
     * @return bool
     */
    public function checkConstraints()
    {
        return isset($this->configuration['check_constraints']) ? $this->configuration['check_constraints'] : true;
    }
}
