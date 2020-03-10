<?php
namespace Ameos\AmeosForm\Elements;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class Text extends ElementAbstract 
{
	
	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) 
	{
		parent::__construct($absolutename, $name, $configuration, $form);
		if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('ameos_form_zipcity_suggest'))
		{
			$this->pageRenderer->addCssFile('/typo3conf/ext/ameos_form_zipcity_suggest/Resources/Public/Css/CitySuggest.css');
			$this->pageRenderer->addJsFooterFile('/typo3conf/ext/ameos_form_zipcity_suggest/Resources/Public/Javascript/CitySuggest.js');
		}
	}

	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() 
	{
		return '<input type="text" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />' . $this->getDatalist();
	}
}
