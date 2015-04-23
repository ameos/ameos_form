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

class Password extends ElementAbstract {
	
	/**
	 * @var bool $searchable searchable
	 */
	protected $searchable = FALSE;
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		return '<input type="password" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />';
	}

	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) {
		$this->valueSetted = TRUE;
		$this->value = $value;
		
		if($this->form->getMode() == 'crud/extbase' && $value != '') {
			$method = 'set' . \Ameos\AmeosForm\Utility\String::camelCase($this->name);
			if(method_exists($this->form->getModel(), $method)) {
				$this->form->getModel()->$method($value);
			}
		}

		if($this->form->getMode() == 'crud/classic' && $value != '') {
			if($this->form->hasData($this->name)) {
				$this->form->setData($this->name, $value);
			}
		}

		return $this;
	}
}
