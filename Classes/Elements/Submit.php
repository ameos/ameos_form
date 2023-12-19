<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Submit extends ElementAbstract
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
        if (isset($this->configuration['src'])) {
            return '<input type="image" src="' . $this->configuration['src'] . '" id="' . $this->getHtmlId() . '" value="' . $this->getLabel() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . ' />';
        } else {
            return '<input type="submit" id="' . $this->getHtmlId() . '" value="' . $this->getLabel() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . ' />';
        }
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
     * return true if must check constraints
     * @return bool
     */
    public function checkConstraints()
    {
        return isset($this->configuration['check_constraints']) ? $this->configuration['check_constraints'] : true;
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
}
