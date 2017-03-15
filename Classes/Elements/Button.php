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

class Button extends ElementAbstract 
{
	
	/**
	 * @var bool $searchable searchable
	 */
	protected $searchable = FALSE;
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() 
	{
		$label = isset($this->configuration['label']) ? $this->configuration['label'] : 'Envoyer';
		
                return '<button id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . '>' . $this->getLabel() . '</button>';
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
	 * return true if the button is clicked
	 * @return bool
	 */
	public function isClicked() 
	{
		if ($this->form->isSubmitted()) {
			$post = GeneralUtility::_POST($this->form->getIdentifier());
			return isset($post[$this->getName()]) && $post[$this->getName()] == $this->getLabel();
		}
		return false;
	}
}
