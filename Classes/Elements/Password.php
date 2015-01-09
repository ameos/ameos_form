<?php

namespace Ameos\AmeosForm\Elements;

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
