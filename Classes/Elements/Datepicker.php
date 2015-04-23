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

use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
		if(!isset($this->configuration['format'])) $this->configuration['format'] = 'D MMM YYYY';

		$GLOBALS['TSFE']->getPageRenderer()->addCssFile('/typo3conf/ext/ameos_form/Resources/Public/Pikaday/css/pikaday.css');
		$GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile('/typo3conf/ext/ameos_form/Resources/Public/Momentjs/moment.js');
		$GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile('/typo3conf/ext/ameos_form/Resources/Public/Pikaday/pikaday.js');
		$GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile('/typo3conf/ext/ameos_form/Resources/Public/Elements/datepicker.js');

		$GLOBALS['TSFE']->getPageRenderer()->addJsFooterInlineCode('init-datepicker-' . $name, '
			var i18n = {
				previousMonth: "' . LocalizationUtility::translate('previousMonth', 'AmeosForm') . '",
				nextMonth: "' . LocalizationUtility::translate('nextMonth', 'AmeosForm') . '",
				months : {
					1: "' . LocalizationUtility::translate('months.1', 'AmeosForm') . '",
					2: "' . LocalizationUtility::translate('months.2', 'AmeosForm') . '",
					3: "' . LocalizationUtility::translate('months.3', 'AmeosForm') . '",
					4: "' . LocalizationUtility::translate('months.4', 'AmeosForm') . '",
					5: "' . LocalizationUtility::translate('months.5', 'AmeosForm') . '",
					6: "' . LocalizationUtility::translate('months.6', 'AmeosForm') . '",
					7: "' . LocalizationUtility::translate('months.7', 'AmeosForm') . '",
					8: "' . LocalizationUtility::translate('months.8', 'AmeosForm') . '",
					9: "' . LocalizationUtility::translate('months.9', 'AmeosForm') . '",
					10: "' . LocalizationUtility::translate('months.10', 'AmeosForm') . '",
					11: "' . LocalizationUtility::translate('months.11', 'AmeosForm') . '",
					12: "' . LocalizationUtility::translate('months.12', 'AmeosForm') . '"
				},
				weekdays : {
					1: "' . LocalizationUtility::translate('weekdays.1', 'AmeosForm') . '",
					2: "' . LocalizationUtility::translate('weekdays.2', 'AmeosForm') . '",
					3: "' . LocalizationUtility::translate('weekdays.3', 'AmeosForm') . '",
					4: "' . LocalizationUtility::translate('weekdays.4', 'AmeosForm') . '",
					5: "' . LocalizationUtility::translate('weekdays.5', 'AmeosForm') . '",
					6: "' . LocalizationUtility::translate('weekdays.6', 'AmeosForm') . '",
					7: "' . LocalizationUtility::translate('weekdays.7', 'AmeosForm') . '"
				},
				weekdaysShort : {
					1: "' . LocalizationUtility::translate('weekdaysShort.1', 'AmeosForm') . '",
					2: "' . LocalizationUtility::translate('weekdaysShort.2', 'AmeosForm') . '",
					3: "' . LocalizationUtility::translate('weekdaysShort.3', 'AmeosForm') . '",
					4: "' . LocalizationUtility::translate('weekdaysShort.4', 'AmeosForm') . '",
					5: "' . LocalizationUtility::translate('weekdaysShort.5', 'AmeosForm') . '",
					6: "' . LocalizationUtility::translate('weekdaysShort.6', 'AmeosForm') . '",
					7: "' . LocalizationUtility::translate('weekdaysShort.7', 'AmeosForm') . '"
				}
			};
			initDatepicker("' . $this->getHtmlId() . '", "' . $this->configuration['format'] . '", i18n);
		');
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		return '<input type="text" id="' . $this->getHtmlId() . '-datepicker" name="' . $this->absolutename . '-datepicker" ' . $this->getAttributes() . ' />'
			. '<input type="hidden" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '" />';
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
		if($value != '') {
			$GLOBALS['TSFE']->getPageRenderer()->addJsFooterInlineCode('setvalue-datepicker-' . $this->getName() . '-' . $value, '
				if(document.getElementById("' . $this->getHtmlId() . '-datepicker")) {
					document.getElementById("' . $this->getHtmlId() . '-datepicker").value = moment(' . $value . ', "X").format("' . $this->configuration['format'] . '");
				}
			');
		} else {
			$GLOBALS['TSFE']->getPageRenderer()->addJsFooterInlineCode('setvalue-datepicker-' . $this->getName() . '-' . $value, '
				if(document.getElementById("' . $this->getHtmlId() . '-datepicker")) {
					document.getElementById("' . $this->getHtmlId() . '-datepicker").value = "";
				}
			');
		}
		return $this;
	}
}
