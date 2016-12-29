<?php
namespace Ameos\AmeosForm\Elements;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class Dropdown extends ElementAbstract 
{

	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) 
	{
		parent::__construct($absolutename, $name, $configuration, $form);
		if (!isset($this->configuration['optionValueField'])) $this->configuration['optionValueField'] = 'uid';
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() 
	{
		if ($this->isMultiple()) {
			$output = '<select id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '[]"' . $this->getAttributes() . '>';
		} else {
			$output = '<select id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . '>';
		}

		if (isset($this->configuration['placeholder'])) {
			$output.= '<option value="">' . $this->configuration['placeholder'] . '</option>';
		}

		$currentValue = $this->getValue();
		if (!is_array($currentValue)) {
			$currentValue = [$currentValue];
		}
		if (is_array($this->configuration['items'])) {
			$optionValueFieldMethod = 'get' . ucfirst($this->configuration['optionValueField']);
			foreach ($currentValue as $currentValueKey => $currentValueItem) {
				if (is_a($currentValueItem, '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {					
					$currentValue[$currentValueKey] = $currentValueItem->$optionValueFieldMethod();
				}
			}

			foreach ($this->configuration['items'] as $value => $label) {
				$selected = in_array($value, $currentValue) ? ' selected="selected"' : '';
				$output.= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
			}
		} elseif (is_object($this->configuration['items']) && is_a($this->configuration['items'], '\\TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult')) {
			$optionLabelFieldMethod = 'get' . ucfirst($this->configuration['optionLabelField']);
			$optionValueFieldMethod = 'get' . ucfirst($this->configuration['optionValueField']);

			foreach ($currentValue as $key => $value) {
				if (is_object($value) && is_a($value, '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {
					$currentValue[$key] = $this->getValue()->$optionValueFieldMethod();
				} elseif (is_object($value) && is_a($value, '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage')) {
					$currentValue = [];
					foreach ($value as $subkey => $subvalue) {
						if (is_object($subvalue) && is_a($subvalue, '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {
							$currentValue[] = $subvalue->$optionValueFieldMethod();
						}
					}
				}
			}

			foreach ($this->configuration['items'] as $model) {
				$selected = in_array($model->$optionValueFieldMethod(), $currentValue) ? ' selected="selected"' : '';
				$output.= '<option value="' . $model->$optionValueFieldMethod() . '"' . $selected . '>' . $model->$optionLabelFieldMethod() . '</option>';
			}			
		}
		$output.= '</select>';
		return $output;
	}

	/**
	 * return html attribute
	 * @return string html attribute
	 */
	public function getAttributes() 
	{
		$output = parent::getAttributes();
		$output.= isset($this->configuration['multiple']) && $this->configuration['multiple'] == true  ? ' multiple="multiple"' : 	'';
		return $output;
	}

	/**
	 * return true if it's a multiple dropdown
	 * @return bool
	 */
	public function isMultiple() 
	{
		return 	isset($this->configuration['multiple']) && $this->configuration['multiple'] == true;
	}
	
	/**
	 * return where clause
	 *
	 * @return	bool|array FALSE if no search. Else array with search type and value
	 */
	public function getClause() 
	{
		$value = $this->getValue();
		if (!empty($value)) {
			if ($this->overrideClause !== false) {
				return parent::getClause();
			} else {
				if ($this->isMultiple()) {
					return [
						'elementname'  => $this->getName(),
						'elementvalue' => $this->getValue(),
						'field' => $this->getSearchField(),
						'type'  => 'in',
						'value' => $value
					];
				} else {
					return [
						'elementname'  => $this->getName(),
						'elementvalue' => $this->getValue(),
						'field' => $this->getSearchField(),
						'type'  => 'equals',
						'value' => $value
					];
				}
			}
		}
		return false;
	}
}
