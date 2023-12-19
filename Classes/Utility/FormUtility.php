<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Elements;
use Ameos\AmeosForm\Enum\Element;

class FormUtility
{
    /**
     * add element fo the form
     *
     * @param   string  $type element type
     * @param   string  $absolutename absolute element name
     * @param   string  $name element name
     * @param   string  $configuration element configuration
     * @param   \Ameos\AmeosForm\Form   $form
     * @return  \Ameos\AmeosForm\Elements\InterfaceElement
     */
    public static function makeElementInstance($absolutename, $name, $type = '', $configuration, $form = false)
    {
        switch ($type) {
            case Element::TEXTAREA:
                $element = GeneralUtility::makeInstance(
                    Elements\Textarea::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::PASSWORD:
                $element = GeneralUtility::makeInstance(
                    Elements\Password::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                )
                    ;
                break;
            case Element::DROPDOWN:
                $element = GeneralUtility::makeInstance(
                    Elements\Dropdown::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::CHECKBOX:
                $element = GeneralUtility::makeInstance(
                    Elements\Checkbox::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::CHECKSINGLE:
                $element = GeneralUtility::makeInstance(
                    Elements\Checksingle::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::SUBMIT:
                $element = GeneralUtility::makeInstance(
                    Elements\Submit::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::HIDDEN:
                $element = GeneralUtility::makeInstance(
                    Elements\Hidden::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::BUTTON:
                $element = GeneralUtility::makeInstance(
                    Elements\Button::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::UPLOAD:
                $element = GeneralUtility::makeInstance(
                    Elements\Upload::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::RADIO:
                $element = GeneralUtility::makeInstance(
                    Elements\Radio::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::DATE:
                $element = GeneralUtility::makeInstance(
                    Elements\Date::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::TEXTDATE:
                $element = GeneralUtility::makeInstance(
                    Elements\Textdate::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::DATEPICKER:
                $element = GeneralUtility::makeInstance(
                    Elements\Datepicker::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::TIME:
                $element = GeneralUtility::makeInstance(
                    Elements\Time::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::TIMEPICKER:
                $element = GeneralUtility::makeInstance(
                    Elements\Timepicker::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::CAPTCHA:
                $element = GeneralUtility::makeInstance(
                    Elements\Captcha::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::NUMERICCAPTCHA:
                $element = GeneralUtility::makeInstance(
                    Elements\Numericcaptcha::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::RECAPTCHA:
                $element = GeneralUtility::makeInstance(
                    Elements\ReCaptcha::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::LABEL:
                $element = GeneralUtility::makeInstance(
                    Elements\Label::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::COLOR:
                $element = GeneralUtility::makeInstance(
                    Elements\Color::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::RANGE:
                $element = GeneralUtility::makeInstance(
                    Elements\Range::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::NUMBER:
                $element = GeneralUtility::makeInstance(
                    Elements\Number::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::EMAIL:
                $element = GeneralUtility::makeInstance(
                    Elements\Email::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::URL:
                $element = GeneralUtility::makeInstance(
                    Elements\Url::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::TEL:
                $element = GeneralUtility::makeInstance(
                    Elements\Tel::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;
            case Element::RATING:
                $element = GeneralUtility::makeInstance(
                    Elements\Rating::class,
                    $absolutename,
                    $name,
                    $configuration,
                    $form
                );
                break;

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
