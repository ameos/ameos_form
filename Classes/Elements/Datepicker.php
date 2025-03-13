<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class Datepicker extends ElementAbstract
{
    /**
     * @constuctor
     *
     * @param   string  $absolutename absolutename
     * @param   string  $name name
     * @param   array   $configuration configuration
     * @param   Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        if (!isset($this->configuration['format'])) {
            $this->configuration['format'] = 'D MMM YYYY';
        }

        // Convert UNIX timestamp (sec) to JavaScript timestamp (millisec)
        if (array_key_exists('minDate', $this->configuration)) {
            $this->configuration['minDate'] *= 1000;
        }
        if (array_key_exists('maxDate', $this->configuration)) {
            $this->configuration['maxDate'] *= 1000;
        }
        if (array_key_exists('disableDays', $this->configuration)) {
            if (array_key_exists('ts', $this->configuration['disableDays'])) {
                foreach ($this->configuration['disableDays']['ts'] as $index => $val) {
                    $this->configuration['disableDays']['ts'][$index] = $val * 1000;
                }
            }
        }
        if (array_key_exists('landingDate', $this->configuration)) {
            $this->configuration['landingDate'] *= 1000;
        }

        $this->assetCollector->addStyleSheet(
            'ameos-form-pikaday',
            'EXT:ameos_form/Resources/Public/Pikaday/css/pikaday.css'
        );
        $this->assetCollector->addJavaScript(
            'ameos-form-moment',
            'EXT:ameos_form/Resources/Public/Momentjs/moment.js'
        );
        $this->assetCollector->addJavaScript(
            'ameos-form-pikaday',
            'EXT:ameos_form/Resources/Public/Pikaday/pikaday.js'
        );
        $this->assetCollector->addJavaScript(
            'ameos-form-datepicker',
            'EXT:ameos_form/Resources/Public/Elements/datepicker.js'
        );

        $this->assetCollector->addInlineJavaScript('init-datepicker-' . $name, '
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
                (isset($this->configuration['minDate'])
                    ? 'configuration.minDate = new Date(' . $this->configuration['minDate'] . ');'
                    : ''
                ) .
                (isset($this->configuration['maxDate'])
                    ? 'configuration.maxDate = new Date(' . $this->configuration['maxDate'] . ');'
                    : ''
                ) .
                /* Array of days to disable eg. mondays, wednesday + 20/02/2020 [d => [1, 3], ts => [1582215827]] */
                /* Array of keyValue pair
                 * ['d' => [1, 3]] for monday, wednesday (0 = sunday, 6 = saturday)
                 * ['m' => [1, 4]] for february, may (0 = january, 11 = december)
                 * ['y' => [2015]] for 2015
                 * ['ts' => [1582215827]] for specific days (20/02/2020)
                 */
                (isset($this->configuration['disableDays'])
                    ? 'configuration.disableDays = ' . \json_encode($this->configuration['disableDays']) . ';'
                    : ''
                ) .
                (isset($this->configuration['landingDate'])
                    ? 'configuration.landingDate = new Date(' . $this->configuration['landingDate'] . ');'
                    : ''
                ) .
                (isset($this->configuration['firstDay'])
                    ? 'configuration.firstDay = ' . $this->configuration['firstDay'] . ';'
                    : ''
                ) .
                (isset($this->configuration['yearRange'])
                    ? 'configuration.yearRange = ' . \json_encode($this->configuration['yearRange']) . '
                    ' :
                '')
            .
            'initDatepicker("'
                . $this->getHtmlId()
                . '", "' . $this->configuration['format'] . '", i18n, configuration);
		');
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml(): string
    {
        $value = $this->getValue();
        if (is_a($value, \DateTimeInterface::class)) {
            $value = $value->getTimestamp();
        }

        return '<input type="text" '
            . 'autocomplete="off" '
            . 'id="' . $this->getHtmlId() . '-datepicker" '
            . 'name="' . $this->absolutename . '-datepicker" '
            . $this->getAttributes() . ' />'
            . '<input type="hidden" '
            . 'id="' . $this->getHtmlId() . '" '
            . 'name="' . $this->absolutename . '" '
            . 'value="' . $value . '" />';
    }

    /**
     * set the value
     *
     * @param   mixed  $value value
     * @return  self this
     */
    public function setValue(mixed $value): self
    {
        if ($value == 0) {
            $value = '';
        }
        parent::setValue($value);
        if (!empty($value)) {
            if (is_a($value, \DateTimeInterface::class)) {
                $value = $value->getTimestamp();
            }
            $this->assetCollector->addInlineJavaScript('setvalue-datepicker-' . $this->getName() . '-' . $value, '
				if(document.getElementById("' . $this->getHtmlId() . '-datepicker")) {
					document.getElementById("' . $this->getHtmlId() . '-datepicker").value = '
                        . 'moment(' . $value . ', "X").format("' . $this->configuration['format'] . '");
				}
			');
        } else {
            $this->assetCollector->addInlineJavaScript('setvalue-datepicker-' . $this->getName() . '-' . $value, '
				if(document.getElementById("' . $this->getHtmlId() . '-datepicker")) {
					document.getElementById("' . $this->getHtmlId() . '-datepicker").value = "";
				}
			');
        }
        return $this;
    }
}
