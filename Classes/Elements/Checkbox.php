<?php

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Checkbox extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';

		$output = '';
		foreach($this->configuration['items'] as $value => $label) {
			$checked = in_array($value, $this->getValue()) ? ' checked="checked"' : '';
			$output.= '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '[]" type="checkbox" value="' . $value . '"' . $checked . $class . ' />' . 
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
				'input' => '<input id="' . $this->getHtmlId() . '-' . $value . '" name="' . $this->absolutename . '[]" type="checkbox" value="' . $value . '"' . $checked . $class . ' />',
				'label' => '<label for="' . $this->getHtmlId() . '-' . $value . '">' . $label . '</label>',
			);
		}
		return $data;
	}

	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) {
		if(is_array($value)) {
			$value = implode(',', $value);
		}
		parent::setValue($value);
		return $this;
	}

	/**
	 * return the value
	 *
	 * @return	string value
	 */
	public function getValue() {
		$value = parent::getValue($value);
		if(!is_array($value)) {
			$value = GeneralUtility::trimExplode(',', $value);
		}
		
		return $value;
	}

	/**
	 * return where clause
	 *
	 * @return	bool|array FALSE if no search. Else array with search type and value
	 */
	public function getClause() {
		$values = $this->getValue();
		if(!empty($values)) {
			if($this->overrideClause !== FALSE) {
				return parent::getClause();
			} else {
				$clauses = [];
				if(is_array($values)) {
					foreach($values as $value) {
						$clauses[] = [
							'field' => $this->getSearchField(),
							'type'  => 'contains',
							'value' => $value,
						];
					}
				}
				return [
					'elementname'  => $this->getName(),
					'elementvalue' => $this->getValue(),
					'field'   => $this->getSearchField(),
					'type'    => 'logicalOr',
					'clauses' => $clauses,
				];
			}
		}
		return FALSE;
	}
}
