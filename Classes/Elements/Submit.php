<?php

namespace Ameos\AmeosForm\Elements;

class Submit extends ElementAbstract {
	
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
		
		return '<input type="submit" id="' . $this->getHtmlId() . '" value="' . $label . '" name="' . $this->absolutename . '"' . $this->getAttributes() . ' />';
	}	
}
