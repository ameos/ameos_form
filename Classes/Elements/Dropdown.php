<?php

namespace Ameos\AmeosForm\Elements;

class Dropdown extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$output = '<select id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . '>';
		if(isset($this->configuration['placeholder'])) {
			$output.= '<option value="">' . $this->configuration['placeholder'] . '</option>';
		}
		if(is_array($this->configuration['items'])) {
			foreach($this->configuration['items'] as $value => $label) {
				$selected = ($this->getValue() == $value) ? ' selected="selected"' : '';
				$output.= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
			}
		} elseif(is_object($this->configuration['items']) && is_a($this->configuration['items'], '\\TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult')) {
			$optionLabelFieldMethod = 'get' . ucfirst($this->configuration['optionLabelField']);
			$optionValueFieldMethod = 'get' . ucfirst($this->configuration['optionValueField']);
			$value = $this->getValue();
			if(is_object($value) && is_a($value, '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {
				$value = $this->getValue()->getUid();
			}

			foreach($this->configuration['items'] as $model) {				
				$selected = ($value == $model->$optionValueFieldMethod()) ? ' selected="selected"' : '';
				$output.= '<option value="' . $model->$optionValueFieldMethod() . '"' . $selected . '>' . $model->$optionLabelFieldMethod() . '</option>';
			}			
		}
		$output.= '</select>';
		return $output;
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
