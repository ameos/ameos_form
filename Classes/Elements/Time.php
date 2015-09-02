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

class Time extends ElementAbstract 
{

	/**
	 * @var string $valueHour current hour value
	 */
	protected $valueHour = '';

	/**
	 * @var string $valueMinute current minute value
	 */
	protected $valueMinute = '';

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
		
		$this->configuration['minutestep'] = isset($this->configuration['minutestep']) ? $this->configuration['minutestep'] : 1;
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() 
	{
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
	public function toHtml() 
	{				
		return $this->renderHour() . ':' . $this->renderMinute();
	}

	/**
	 * return hour html selector
	 *
	 * @return	string
	 */
	public function renderHour() 
	{
		$output = '<input type="text" id="' . $this->getHtmlId() . '-hour" list="' . $this->getHtmlId() . '-hour-datalist" size="2" name="' . $this->absolutename . '[hour]" value="' . $this->valueHour . '"' . $this->getAttributes() . ' placeholder="hh" />';
		$output.= '<datalist id="' . $this->getHtmlId() . '-hour-datalist">';
		for ($hour = 0; $hour < 24; $hour++) {
			$output.= '<option value="' . str_pad($hour, 2, '0', STR_PAD_LEFT) . '">' . str_pad($hour, 2, '0', STR_PAD_LEFT) . '</option>';	
		}
		$output.= '</datalist>';
		return $output;
	}

	/**
	 * return minute html selector
	 *
	 * @return	string
	 */
	public function renderMinute() 
	{
		$output = '<input type="text" id="' . $this->getHtmlId() . '-minute" list="' . $this->getHtmlId() . '-minute-datalist" size="2" name="' . $this->absolutename . '[minute]" value="' . $this->valueMinute . '"' . $this->getAttributes() . ' placeholder="mm" />';
		$output.= '<datalist id="' . $this->getHtmlId() . '-minute-datalist">';
		for ($minute = 0; $minute < 60; $minute+= $this->configuration['minutestep']) {
			$output.= '<option value="' . str_pad($minute, 2, '0', STR_PAD_LEFT) . '">' . str_pad($minute, 2, '0', STR_PAD_LEFT) . '</option>';	
		}
		$output.= '</datalist>';
		return $output;
	}
	
	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) 
	{
		if (is_array($value)) {
			$this->valueHour   = $value['hour'];
			$this->valueMinute = $value['minute'];

			$value = $this->valueMinute . $this->valueHour;
		}
		parent::setValue($value);
		return $this;
	}
}

