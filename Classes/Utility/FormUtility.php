<?php
namespace Ameos\AmeosForm\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FormUtility 
{

	/**
	 * add element fo the form
	 * 
	 * @param	string	$type element type
	 * @param	string	$absolutename absolute element name
	 * @param	string	$name element name
	 * @param	string	$configuration element configuration
	 * @param	\Ameos\AmeosForm\Form	$form
	 * @return	\Ameos\AmeosForm\Elements\InterfaceElement
	 */
	public static function makeElementInstance($absolutename, $name, $type = '', $configuration, $form = false) 
	{
		switch ($type) {
			case 'textarea':    $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Textarea',    $absolutename, $name, $configuration, $form); break;
			case 'password':    $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Password',    $absolutename, $name, $configuration, $form); break;
			case 'dropdown':    $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Dropdown',    $absolutename, $name, $configuration, $form); break;
			case 'checkbox':    $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Checkbox',    $absolutename, $name, $configuration, $form); break;
			case 'checksingle': $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Checksingle', $absolutename, $name, $configuration, $form); break;
 			case 'submit':      $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Submit',      $absolutename, $name, $configuration, $form); break;
			case 'hidden':      $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Hidden',      $absolutename, $name, $configuration, $form); break;
			case 'button':      $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Button',      $absolutename, $name, $configuration, $form); break;
			case 'upload':      $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Upload',      $absolutename, $name, $configuration, $form); break;
			case 'radio':       $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Radio',       $absolutename, $name, $configuration, $form); break;
			case 'date':        $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Date',        $absolutename, $name, $configuration, $form); break;
			case 'datepicker':  $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Datepicker',  $absolutename, $name, $configuration, $form); break;
			case 'time':        $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Time',        $absolutename, $name, $configuration, $form); break;
			case 'timepicker':  $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Timepicker',  $absolutename, $name, $configuration, $form); break;
			case 'captcha':     $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Captcha',     $absolutename, $name, $configuration, $form); break;
			case 'recaptcha':   $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\ReCaptcha',   $absolutename, $name, $configuration, $form); break;
			case 'label':       $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Label',       $absolutename, $name, $configuration, $form); break;
			case 'color':       $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Color',       $absolutename, $name, $configuration, $form); break;
			case 'range':       $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Range',       $absolutename, $name, $configuration, $form); break;
			case 'number':      $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Number',      $absolutename, $name, $configuration, $form); break;
			case 'email':       $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Email',       $absolutename, $name, $configuration, $form); break;
			case 'url':         $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Url',         $absolutename, $name, $configuration, $form); break;
			case 'tel':         $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Tel',         $absolutename, $name, $configuration, $form); break;
			case 'rating':      $element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Rating',      $absolutename, $name, $configuration, $form); break;
			
			default:
				if ($type != '' && $type != 'text' && class_exists($type)) {
					$element = GeneralUtility::makeInstance($type, $absolutename, $name, $configuration, $form);
				} else {
					$element = GeneralUtility::makeInstance('Ameos\\AmeosForm\\Elements\\Text', $absolutename, $name, $configuration, $form);
				}
				break;
		}

		if (!is_a($element, '\\Ameos\\AmeosForm\\Elements\\ElementInterface')) {
			throw new \Exception(get_class($element) . ' must implements \\Ameos\\AmeosForm\\Elements\\ElementInterface');
		}

		return $element;
	}
}
