<?php

namespace Ameos\AmeosForm\Elements;

class Range extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$additionnalAttributes = '';
		if(isset($this->configuration['min']))  { $additionnalAttributes.= ' min="' . $this->configuration['min'] . '"'; }
		if(isset($this->configuration['max']))  { $additionnalAttributes.= ' max="' . $this->configuration['max'] . '"'; }
		if(isset($this->configuration['step']))  { $additionnalAttributes.= ' step="' . $this->configuration['step'] . '"'; }
		
		return '<input type="range" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . $additionnalAttributes . ' />' . $this->getDatalist();
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = parent::getRenderingInformation();

		if(isset($this->configuration['min']))  { $data['min']  = $this->configuration['min']; }
		if(isset($this->configuration['max']))  { $data['max']  = $this->configuration['max']; }
		if(isset($this->configuration['step'])) { $data['step'] = $this->configuration['step']; }
		
		return $data;
	}
}
