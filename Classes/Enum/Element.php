<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Enum;

use Ameos\AmeosForm\Elements;
use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Exception\TypeNotFoundException;

/**
 * Element types usable with Form::add(). Kept as string constants for backwards compatibility.
 */
enum Element: string
{
    public const string TEXT = 'text';
    public const string TEXTAREA = 'textarea';
    public const string PASSWORD = 'password';
    public const string DROPDOWN = 'dropdown';
    public const string CHECKBOX = 'checkbox';
    public const string CHECKSINGLE = 'checksingle';
    public const string SUBMIT = 'submit';
    public const string HIDDEN = 'hidden';
    public const string BUTTON = 'button';
    public const string UPLOAD = 'upload';
    public const string RADIO = 'radio';
    public const string DATE = 'date';
    public const string TEXTDATE = 'textdate';
    public const string TIME = 'time';
    public const string NUMERICCAPTCHA = 'numericcaptcha';
    public const string RECAPTCHA = 'recaptcha';
    public const string COLOR = 'color';
    public const string RANGE = 'range';
    public const string NUMBER = 'number';
    public const string EMAIL = 'email';
    public const string URL = 'url';
    public const string TEL = 'tel';
    public const string RATING = 'rating';

    /**
     * @var array<string, class-string<ElementInterface>>
     */
    private const CLASS_NAMES = [
        self::TEXT => Elements\Text::class,
        self::TEXTAREA => Elements\Textarea::class,
        self::PASSWORD => Elements\Password::class,
        self::DROPDOWN => Elements\Dropdown::class,
        self::CHECKBOX => Elements\Checkbox::class,
        self::CHECKSINGLE => Elements\Checksingle::class,
        self::SUBMIT => Elements\Submit::class,
        self::HIDDEN => Elements\Hidden::class,
        self::BUTTON => Elements\Button::class,
        self::UPLOAD => Elements\Upload::class,
        self::RADIO => Elements\Radio::class,
        self::DATE => Elements\Date::class,
        self::TEXTDATE => Elements\Textdate::class,
        self::TIME => Elements\Time::class,
        self::NUMERICCAPTCHA => Elements\Numericcaptcha::class,
        self::RECAPTCHA => Elements\ReCaptcha::class,
        self::COLOR => Elements\Color::class,
        self::RANGE => Elements\Range::class,
        self::NUMBER => Elements\Number::class,
        self::EMAIL => Elements\Email::class,
        self::URL => Elements\Url::class,
        self::TEL => Elements\Tel::class,
        self::RATING => Elements\Rating::class,
    ];

    /**
     * @return class-string<ElementInterface>
     * @throws TypeNotFoundException
     */
    public static function getElementClassName(string $element): string
    {
        return self::CLASS_NAMES[$element]
            ?? throw new TypeNotFoundException(sprintf('element %s not found', $element), 1731340800);
    }
}
