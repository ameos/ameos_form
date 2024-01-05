<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Utility;

use Ameos\AmeosForm\Constraints;
use Ameos\AmeosForm\Elements;
use Ameos\AmeosForm\Enum\Constraint;
use Ameos\AmeosForm\Enum\Element;
use Ameos\AmeosForm\Exception\TypeNotFoundException;

class FormUtility
{
    /**
     * return class name of constraint $type
     *
     * @param string  $type element type
     * @return string
     */
    public static function getConstrainClassNameByType(string $type): string
    {
        $className = '';
        switch ($type) {
            case Constraint::EMAIL:
                $className = Constraints\Email::class;
                break;
            case Constraint::UNIQUE:
                $className = Constraints\Unique::class;
                break;
            case Constraint::FILEEXTENSION:
                $className = Constraints\Fileextension::class;
                break;
            case Constraint::FILESIZE:
                $className = Constraints\Filesize::class;
                break;
            case Constraint::CUSTOM:
                $className = Constraints\Custom::class;
                break;
            case Constraint::REQUIRED:
                $className = Constraints\Required::class;
                break;
            default:
                throw new TypeNotFoundException(sprintf('constraint %s not found', $type));
                break;
        }
        return $className;
    }

    /**
     * return class name of element $type
     *
     * @param string  $type element type
     * @return string
     */
    public static function getElementClassNameByType(string $type): string
    {
        $className = '';
        switch ($type) {
            case Element::TEXT:
                $className = Elements\Text::class;
                break;
            case Element::TEXTAREA:
                $className = Elements\Textarea::class;
                break;
            case Element::PASSWORD:
                $className = Elements\Password::class;
                break;
            case Element::DROPDOWN:
                $className = Elements\Dropdown::class;
                break;
            case Element::CHECKBOX:
                $className = Elements\Checkbox::class;
                break;
            case Element::CHECKSINGLE:
                $className = Elements\Checksingle::class;
                break;
            case Element::SUBMIT:
                $className = Elements\Submit::class;
                break;
            case Element::HIDDEN:
                $className = Elements\Hidden::class;
                break;
            case Element::BUTTON:
                $className = Elements\Button::class;
                break;
            case Element::UPLOAD:
                $className = Elements\Upload::class;
                break;
            case Element::RADIO:
                $className = Elements\Radio::class;
                break;
            case Element::DATE:
                $className = Elements\Date::class;
                break;
            case Element::TEXTDATE:
                $className = Elements\Textdate::class;
                break;
            case Element::DATEPICKER:
                $className = Elements\Datepicker::class;
                break;
            case Element::TIME:
                $className = Elements\Time::class;
                break;
            case Element::TIMEPICKER:
                $className = Elements\Timepicker::class;
                break;
            case Element::NUMERICCAPTCHA:
                $className = Elements\Numericcaptcha::class;
                break;
            case Element::RECAPTCHA:
                $className = Elements\ReCaptcha::class;
                break;
            case Element::LABEL:
                $className = Elements\Label::class;
                break;
            case Element::COLOR:
                $className = Elements\Color::class;
                break;
            case Element::RANGE:
                $className = Elements\Range::class;
                break;
            case Element::NUMBER:
                $className = Elements\Number::class;
                break;
            case Element::EMAIL:
                $className = Elements\Email::class;
                break;
            case Element::URL:
                $className = Elements\Url::class;
                break;
            case Element::TEL:
                $className = Elements\Tel::class;
                break;
            case Element::RATING:
                $className = Elements\Rating::class;
                break;
            default:
                throw new TypeNotFoundException(sprintf('element %s not found', $type));
                break;
        }
        return $className;
    }
}
