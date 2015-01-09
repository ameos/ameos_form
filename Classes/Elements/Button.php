<?php

namespace Ameos\AmeosForm\Elements;

class Button extends ElementAbstract {
	
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
		$label = isset($this->configuration['label']) ? $this->configuration['label'] : 'Envoyer';
		
		return '<input type="button" id="' . $this->getHtmlId() . '" value="' . $label . '" name="' . $this->absolutename . '"' . $this->getAttributes() . ' />';
	}	
}
