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

class Time extends ElementAbstract {

	/**
	 * @var string $valueHour current hour value
	 */
	protected $valueHour = '';

	/**
	 * @var string $valueMinute current minute value
	 */
	protected $valueMinute = '';

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = parent::getRenderingInformation();
		$data['hour']   = $this->renderHour();
		$data['minute'] = $this->renderMinute();
		return $data;
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {				
		return $this->renderHour() . ':' . $this->renderMinute();
	}

	/**
	 * return hour html selector
	 *
	 * @return	string
	 */
	public function renderHour() {
		return '<input type="text" id="' . $this->getHtmlId() . '-hour" size="2" name="' . $this->absolutename . '[hour]" value="' . $this->valueHour . '"' . $this->getAttributes() . ' placeholder="hh" />';
	}

	/**
	 * return minute html selector
	 *
	 * @return	string
	 */
	public function renderMinute() {
		return '<input type="text" id="' . $this->getHtmlId() . '-minute" size="2" name="' . $this->absolutename . '[minute]" value="' . $this->valueMinute . '"' . $this->getAttributes() . ' placeholder="mm" />';
	}
	
	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) {
		if(is_array($value)) {
			$this->valueHour   = $value['hour'];
			$this->valueMinute = $value['minute'];

			$value = $this->valueMinute . $this->valueHour;
		}
		parent::setValue($value);
		return $this;
	}
}

