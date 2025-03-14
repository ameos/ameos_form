<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Enum;

use Ameos\AmeosForm\Elements;
use Ameos\AmeosForm\Exception\TypeNotFoundException;

enum Element: string
{
    public const TEXT = 'text';
    public const TEXTAREA = 'textarea';
    public const PASSWORD = 'password';
    public const DROPDOWN = 'dropdown';
    public const CHECKBOX = 'checkbox';
    public const CHECKSINGLE = 'checksingle';
    public const SUBMIT = 'submit';
    public const HIDDEN = 'hidden';
    public const BUTTON = 'button';
    public const UPLOAD = 'upload';
    public const RADIO = 'radio';
    public const DATE = 'date';
    public const TEXTDATE = 'textdate';
    public const TIME = 'time';
    public const TIMEPICKER = 'timepicker';
    public const NUMERICCAPTCHA = 'numericcaptcha';
    public const RECAPTCHA = 'recaptcha';
    public const COLOR = 'color';
    public const RANGE = 'range';
    public const NUMBER = 'number';
    public const EMAIL = 'email';
    public const URL = 'url';
    public const TEL = 'tel';
    public const RATING = 'rating';

    public static function getElementClassName(string $element): string
    {
        $className = '';
        switch ($element) {
            case self::TEXT:
                $className = Elements\Text::class;
                break;
            case self::TEXTAREA:
                $className = Elements\Textarea::class;
                break;
            case self::PASSWORD:
                $className = Elements\Password::class;
                break;
            case self::DROPDOWN:
                $className = Elements\Dropdown::class;
                break;
            case self::CHECKBOX:
                $className = Elements\Checkbox::class;
                break;
            case self::CHECKSINGLE:
                $className = Elements\Checksingle::class;
                break;
            case self::SUBMIT:
                $className = Elements\Submit::class;
                break;
            case self::HIDDEN:
                $className = Elements\Hidden::class;
                break;
            case self::BUTTON:
                $className = Elements\Button::class;
                break;
            case self::UPLOAD:
                $className = Elements\Upload::class;
                break;
            case self::RADIO:
                $className = Elements\Radio::class;
                break;
            case self::DATE:
                $className = Elements\Date::class;
                break;
            case self::TEXTDATE:
                $className = Elements\Textdate::class;
                break;
            case self::TIME:
                $className = Elements\Time::class;
                break;
            case self::TIMEPICKER:
                $className = Elements\Timepicker::class;
                break;
            case self::NUMERICCAPTCHA:
                $className = Elements\Numericcaptcha::class;
                break;
            case self::RECAPTCHA:
                $className = Elements\ReCaptcha::class;
                break;
            case self::COLOR:
                $className = Elements\Color::class;
                break;
            case self::RANGE:
                $className = Elements\Range::class;
                break;
            case self::NUMBER:
                $className = Elements\Number::class;
                break;
            case self::EMAIL:
                $className = Elements\Email::class;
                break;
            case self::URL:
                $className = Elements\Url::class;
                break;
            case self::TEL:
                $className = Elements\Tel::class;
                break;
            case self::RATING:
                $className = Elements\Rating::class;
                break;
            default:
                throw new TypeNotFoundException(sprintf('element %s not found', $element));
        }
        return $className;
    }
}
