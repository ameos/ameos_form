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
use Ameos\AmeosForm\Elements;

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
			case 'textarea':       $element = GeneralUtility::makeInstance(Elements\Textarea::class,       $absolutename, $name, $configuration, $form); break;
			case 'password':       $element = GeneralUtility::makeInstance(Elements\Password::class,       $absolutename, $name, $configuration, $form); break;
			case 'dropdown':       $element = GeneralUtility::makeInstance(Elements\Dropdown::class,       $absolutename, $name, $configuration, $form); break;
			case 'checkbox':       $element = GeneralUtility::makeInstance(Elements\Checkbox::class,       $absolutename, $name, $configuration, $form); break;
			case 'checksingle':    $element = GeneralUtility::makeInstance(Elements\Checksingle::class,    $absolutename, $name, $configuration, $form); break;
 			case 'submit':         $element = GeneralUtility::makeInstance(Elements\Submit::class,         $absolutename, $name, $configuration, $form); break;
			case 'hidden':         $element = GeneralUtility::makeInstance(Elements\Hidden::class,         $absolutename, $name, $configuration, $form); break;
			case 'button':         $element = GeneralUtility::makeInstance(Elements\Button::class,         $absolutename, $name, $configuration, $form); break;
			case 'upload':         $element = GeneralUtility::makeInstance(Elements\Upload::class,         $absolutename, $name, $configuration, $form); break;
			case 'radio':          $element = GeneralUtility::makeInstance(Elements\Radio::class,          $absolutename, $name, $configuration, $form); break;
			case 'date':           $element = GeneralUtility::makeInstance(Elements\Date::class,           $absolutename, $name, $configuration, $form); break;
			case 'datepicker':     $element = GeneralUtility::makeInstance(Elements\Datepicker::class,     $absolutename, $name, $configuration, $form); break;
			case 'time':           $element = GeneralUtility::makeInstance(Elements\Time::class,           $absolutename, $name, $configuration, $form); break;
			case 'timepicker':     $element = GeneralUtility::makeInstance(Elements\Timepicker::class,     $absolutename, $name, $configuration, $form); break;
			case 'captcha':        $element = GeneralUtility::makeInstance(Elements\Captcha::class,        $absolutename, $name, $configuration, $form); break;
			case 'numericcaptcha': $element = GeneralUtility::makeInstance(Elements\Numericcaptcha::class, $absolutename, $name, $configuration, $form); break;
			case 'recaptcha':      $element = GeneralUtility::makeInstance(Elements\ReCaptcha::class,      $absolutename, $name, $configuration, $form); break;
			case 'label':          $element = GeneralUtility::makeInstance(Elements\Label::class,          $absolutename, $name, $configuration, $form); break;
			case 'color':          $element = GeneralUtility::makeInstance(Elements\Color::class,          $absolutename, $name, $configuration, $form); break;
			case 'range':          $element = GeneralUtility::makeInstance(Elements\Range::class,          $absolutename, $name, $configuration, $form); break;
			case 'number':         $element = GeneralUtility::makeInstance(Elements\Number::class,         $absolutename, $name, $configuration, $form); break;
			case 'email':          $element = GeneralUtility::makeInstance(Elements\Email::class,          $absolutename, $name, $configuration, $form); break;
			case 'url':            $element = GeneralUtility::makeInstance(Elements\Url::class,            $absolutename, $name, $configuration, $form); break;
			case 'tel':            $element = GeneralUtility::makeInstance(Elements\Tel::class,            $absolutename, $name, $configuration, $form); break;
			case 'rating':         $element = GeneralUtility::makeInstance(Elements\Rating::class,         $absolutename, $name, $configuration, $form); break;
			
			default:
				if ($type != '' && $type != 'text' && class_exists($type)) {
					$element = GeneralUtility::makeInstance($type, $absolutename, $name, $configuration, $form);
				} else {
					$element = GeneralUtility::makeInstance(Elements\Text::class, $absolutename, $name, $configuration, $form);
				}
				break;
		}

		if (!is_a($element, Elements\ElementInterface::class)) {
			throw new \Exception(get_class($element) . ' must implements ' . Elements\ElementInterface::class);
		}

		return $element;
	}
}
