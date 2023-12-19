<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Text extends ElementAbstract
{
    /**
     * @constuctor
     *
     * @param   string  $absolutename absolutename
     * @param   string  $name name
     * @param   array   $configuration configuration
     * @param   \Ameos\AmeosForm\Form $form form
     */
    public function __construct($absolutename, $name, $configuration, $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('ameos_form_zipcity_suggest')) {
            $this->pageRenderer->addCssFile('/typo3conf/ext/ameos_form_zipcity_suggest/Resources/Public/Css/CitySuggest.css');
            $this->pageRenderer->addJsFooterFile('/typo3conf/ext/ameos_form_zipcity_suggest/Resources/Public/Javascript/CitySuggest.js');
        }
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        return '<input type="text" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />' . $this->getDatalist();
    }
}
