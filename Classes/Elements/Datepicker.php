<?php

namespace Ameos\AmeosForm\Elements;

class Datepicker extends ElementAbstract {

	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) {
		parent::__construct($absolutename, $name, $configuration, $form);
		if(!isset($this->configuration['format'])) $this->configuration['format'] = '%d/%m/%Y';
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		return '<input type="text" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValueToDisplay() . '"' . $this->getAttributes() . ' />';
	}

	/**
	 * Return value to display
	 *
	 * @return string
	 */
	public function getValueToDisplay() {
		$value = $this->getValue();
		if($value == '0' || $value == '') {
			return '';
		}
		
		if(is_numeric($value)) {
			return strftime($this->configuration['format'], $value);
		}
		
		return $value;
	}

	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) {
		if(!is_numeric($value)) {
			$value = \Ameos\AmeosForm\Utility\Date::dateToTimestamp($value, $this->configuration['format']);
		}
		if($value == 0) {
			$value = '';
		}
		parent::setValue($value);
		return $this;
	}
}
