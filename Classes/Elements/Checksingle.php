<?php

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Checksingle extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$checked = ($this->getValue() == 1) ? ' checked="checked"' : '';
		
		return '<input type="checkbox" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="1"' . $this->getAttributes() . $checked . ' />';
	}
}
