<?php

namespace Ameos\AmeosForm\Elements;

class Radio extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';
		
		$output = '';
		foreach($this->configuration['items'] as $value => $label) {
			$checked = ($this->getValue() == $value) ? ' checked="checked"' : '';
			$output.= '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '" type="radio" value="' . $value . '"' . $checked . $class . ' />' . 
				'<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label><br />';
		}
		return $output;
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = parent::getRenderingInformation();
		$data['items'] = [];
		foreach($this->configuration['items'] as $value => $label) {
			$data['items'][$value] = array(
				'input' => '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '" type="radio" value="' . $value . '"' . $checked . $class . ' />',
				'label' => '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>',
			);
		}
		return $data;
	}
	
	/**
	 * return where clause
	 *
	 * @return	bool|array FALSE if no search. Else array with search type and value
	 */
	public function getClause() {
		if($this->getValue() != '') {
			if($this->overrideClause !== FALSE) {
				return parent::getClause();
			} else {
				return [
					'elementname'  => $this->getName(),
					'elementvalue' => $this->getValue(),
					'field' => $this->getSearchField(),
					'type'  => 'equals',
					'value' => $this->getValue()
				];
			}
		}
		return FALSE;
	}
}
