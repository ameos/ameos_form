<?php

namespace Ameos\AmeosForm\Elements;

class Date extends ElementAbstract {

	/**
	 * @var string $valueYear current year value
	 */
	protected $valueYear = '';

	/**
	 * @var string $valueMonth current month value
	 */
	protected $valueMonth = '';

	/**
	 * @var string $valueDay current day value
	 */
	protected $valueDay = '';

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
		if(!isset($this->configuration['format-output'])) {
			$this->configuration['format-output'] = 'timestamp';
		}
		if(!isset($this->configuration['format-display'])) {
			$this->configuration['format-display'] = 'dmy';
		}
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = parent::getRenderingInformation();
		$data['year']  = $this->renderYear();
		$data['month'] = $this->renderMonth();
		$data['day']   = $this->renderDay();
		return $data;
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$output = '';
		switch(substr($this->configuration['format-display'], 0, 1)) {
			case 'd': $output.= $this->renderDay();   break;
			case 'm': $output.= $this->renderMonth(); break;
			case 'y': $output.= $this->renderYear();  break;
		}

		switch(substr($this->configuration['format-display'], 1, 1)) {
			case 'd': $output.= $this->renderDay();   break;
			case 'm': $output.= $this->renderMonth(); break;
			case 'y': $output.= $this->renderYear();  break;
		}

		switch(substr($this->configuration['format-display'], 2, 1)) {
			case 'd': $output.= $this->renderDay();   break;
			case 'm': $output.= $this->renderMonth(); break;
			case 'y': $output.= $this->renderYear();  break;
		}
		
		return $output;
	}

	/**
	 * return year html selector
	 *
	 * @return	string
	 */
	public function renderYear() {
		$cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';
		
		$availableYears = $this->getYearsItems();
		
		$outputYears = '<select id="' . $this->getHtmlId() . '-year" name="' . $this->absolutename . '[year]"' . $cssclass  . '>';
		foreach($availableYears as $year) {
			$selected = ($this->valueYear == $year) ? ' selected="selected"' : '';
			$outputYears.= '<option value="' . $year . '"' . $selected . '>' . $year . '</option>';
		}
		$outputYears.= '</select>';
		return $outputYears;
	}

	/**
	 * return month html selector
	 *
	 * @return	string
	 */
	public function renderMonth() {
		$cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';
		
		$availableMonths = $this->getMonthsItems();
		
		$outputMonths = '<select id="' . $this->getHtmlId() . '-month" name="' . $this->absolutename . '[month]"' . $cssclass  . '>';
		foreach($availableMonths as $month) {
			$selected = ($this->valueMonth == $month) ? ' selected="selected"' : '';
			if($month != '') {
				$outputMonths.= '<option value="' . $month . '"' . $selected . '>' . strftime('%B', mktime(0, 0, 0, $month)) . '</option>';
			} else {
				$outputMonths.= '<option value=""' . $selected . '></option>';
			}
		}
		$outputMonths.= '</select>';
		return $outputMonths;
	}

	/**
	 * return day html selector
	 *
	 * @return	string
	 */
	public function renderDay() {
		$cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';
		
		$availableDays = $this->getDaysItems();

		$outputDays = '<select id="' . $this->getHtmlId() . '-day" name="' . $this->absolutename . '[day]"' . $cssclass  . '>';
		foreach($availableDays as $day) {
			$selected = ($this->valueDay == $day) ? ' selected="selected"' : '';
			$outputDays.= '<option value="' . $day . '"' . $selected . '>' . $day . '</option>';
		}
		$outputDays.= '</select>';
		return $outputDays;
	}

	/**
	 * return available years value
	 * @return array
	 */ 
	protected function getYearsItems() {
		/*
		when yield will avaiblable on most of server
		for($year = 1900; $year <= date('Y') + 20; $year++) {
			yield $year;
		}
		*/
		$years = [''] ;
		for($year = date('Y') + 20; $year >= 1900; $year--) {
			$years[] = $year;
		}
		return $years;
	}

	/**
	 * return available months value
	 * @return array
	 */
	protected function getMonthsItems() {
		/*
		when yield will avaiblable on most of server
		for($day = 1; $day <= 31; $day++) {
			yield $day;
		}
		*/
		$months = [''] ;
		for($month = 1; $month <= 12; $month++) {
			$months[] = $month;
		}
		return $months;
	}

	/**
	 * return available days value
	 * @return array
	 */
	protected function getDaysItems() {
		/*
		when yield will avaiblable on most of server
		for($day = 1; $day <= 31; $day++) {
			yield $day;
		}
		*/
		$days = [''] ;
		for($day = 1; $day <= 31; $day++) {
			$days[] = $day;
		}
		return $days;
	}

	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) {
		if(is_array($value)) {
			$this->valueDay   = $value['day'];
			$this->valueMonth = $value['month'];
			$this->valueYear  = $value['year'];

			if($this->valueDay == '' || $this->valueMonth == '' || $this->valueYear == '') {
				$value = '';
			} else {
				if(!checkdate($this->valueMonth, $this->valueDay, $this->valueYear)) {
					$this->systemerror[] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('error.date.valid', 'AmeosForm');
					return $this;
				}
				$date  = new \Datetime($value['year'] . '-' . $value['month'] . '-' . $value['day']);
				$value = $date->getTimestamp();
			}
			
		} elseif(is_a($value, '\Datetime')) {
			$value = $date->getTimestamp();

			$this->valueDay   = date('j', $value);
			$this->valueMonth = date('n', $value);
			$this->valueYear  = date('Y', $value);			
		} elseif(is_numeric($value)) {
			$this->valueDay   = date('j', $value);
			$this->valueMonth = date('n', $value);
			$this->valueYear  = date('Y', $value);	
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
		$value = parent::getValue();
		if($value == '') return '';
		
		$value = date('Y-m-d', $value);
		$date  = new \Datetime($value);
		if($this->configuration['format-output'] == 'timestamp') {
			return $date->getTimestamp();
		}

		return $date->format($this->configuration['format-output']);
	}
}

