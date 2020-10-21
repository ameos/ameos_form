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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Datepicker extends ElementAbstract 
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
        
		if (!isset($this->configuration['format'])) $this->configuration['format'] = 'D MMM YYYY';

		// Convert UNIX timestamp (sec) to JavaScript timestamp (millisec)
		if(array_key_exists('minDate', $this->configuration))
			$this->configuration['minDate'] *= 1000;
		if(array_key_exists('maxDate', $this->configuration))
			$this->configuration['maxDate'] *= 1000;
		if(array_key_exists('disableDays', $this->configuration))
		{
			if(array_key_exists('ts', $this->configuration['disableDays']))
			{
				foreach($this->configuration['disableDays']['ts'] as $index => $val)
					$this->configuration['disableDays']['ts'][$index] = $val * 1000;
			}
		}
		if(array_key_exists('landingDate', $this->configuration))
			$this->configuration['landingDate'] *= 1000;

		$this->pageRenderer->addCssFile('/typo3conf/ext/ameos_form/Resources/Public/Pikaday/css/pikaday.css');
		$this->pageRenderer->addJsFooterFile('/typo3conf/ext/ameos_form/Resources/Public/Momentjs/moment.js');
		$this->pageRenderer->addJsFooterFile('/typo3conf/ext/ameos_form/Resources/Public/Pikaday/pikaday.js');
		$this->pageRenderer->addJsFooterFile('/typo3conf/ext/ameos_form/Resources/Public/Elements/datepicker.js');

		$this->pageRenderer->addJsFooterInlineCode('init-datepicker-' . $name, '
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
			var configuration = {};'
			.
				/* *1000 to convert unix timestamp to js timestamp */
				(isset($this->configuration['minDate']) ? 'configuration.minDate = new Date(' . $this->configuration['minDate'] . ');' : '') .
				(isset($this->configuration['maxDate']) ? 'configuration.maxDate = new Date(' . $this->configuration['maxDate'] . ');' : '') .
				/* Array of days to disable eg. mondays, wednesday + 20/02/2020 [d => [1, 3], ts => [1582215827]] */
				/* Array of keyValue pair
				 * ['d' => [1, 3]] for monday, wednesday (0 = sunday, 6 = saturday)
				 * ['m' => [1, 4]] for february, may (0 = january, 11 = december)
				 * ['y' => [2015]] for 2015
				 * ['ts' => [1582215827]] for specific days (20/02/2020)
				 */
				(isset($this->configuration['disableDays']) ? 'configuration.disableDays = ' . \json_encode($this->configuration['disableDays']) . ';' : '') .
				(isset($this->configuration['landingDate']) ? 'configuration.landingDate = new Date(' . $this->configuration['landingDate'] . ');' : '') . 
				(isset($this->configuration['firstDay']) ? 'configuration.firstDay = ' . $this->configuration['firstDay'] . ';' : '') .
				(isset($this->configuration['yearRange']) ? 'configuration.yearRange = ' . \json_encode($this->configuration['yearRange']) . ';' : '')
			.
			'initDatepicker("' . $this->getHtmlId() . '", "' . $this->configuration['format'] . '", i18n, configuration);
		');
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() 
	{
		return '<input type="text" autocomplete="off" id="' . $this->getHtmlId() . '-datepicker" name="' . $this->absolutename . '-datepicker" ' . $this->getAttributes() . ' />'
			. '<input type="hidden" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '" />';
	}

	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) 
	{
		if ($value == 0) {
			$value = '';
		}
		parent::setValue($value);
		if ($value != '') {
			$this->pageRenderer->addJsFooterInlineCode('setvalue-datepicker-' . $this->getName() . '-' . $value, '
				if(document.getElementById("' . $this->getHtmlId() . '-datepicker")) {
					document.getElementById("' . $this->getHtmlId() . '-datepicker").value = moment(' . $value . ', "X").format("' . $this->configuration['format'] . '");
				}
			');
		} else {
			$this->pageRenderer->addJsFooterInlineCode('setvalue-datepicker-' . $this->getName() . '-' . $value, '
				if(document.getElementById("' . $this->getHtmlId() . '-datepicker")) {
					document.getElementById("' . $this->getHtmlId() . '-datepicker").value = "";
				}
			');
		}
		return $this;
	}
}
