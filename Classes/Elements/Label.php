<?php

namespace Ameos\AmeosForm\Elements;

class Label extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		return $this->getValue();
	}	
}
