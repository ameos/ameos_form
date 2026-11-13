<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Enum;

use Ameos\AmeosForm\Constraints;
use Ameos\AmeosForm\Constraints\ConstraintInterface;
use Ameos\AmeosForm\Exception\TypeNotFoundException;

/**
 * Constraint types usable with Form::addConstraint(). Kept as string constants for backwards compatibility.
 */
enum Constraint: string
{
    public const string EMAIL = 'email';
    public const string SAMEAS = 'sameas';
    public const string UNIQUE = 'unique';
    public const string FILEEXTENSION = 'fileextension';
    public const string FILEMIME = 'filemime';
    /** Declared for backwards compatibility, no implementation exists (use the "maxSize" upload option) */
    public const string FILESIZE = 'filesize';
    public const string CUSTOM = 'custom';
    public const string REQUIRED = 'required';

    /**
     * @var array<string, class-string<ConstraintInterface>>
     */
    private const CLASS_NAMES = [
        self::EMAIL => Constraints\Email::class,
        self::UNIQUE => Constraints\Unique::class,
        self::FILEEXTENSION => Constraints\Fileextension::class,
        self::FILEMIME => Constraints\Filemime::class,
        self::CUSTOM => Constraints\Custom::class,
        self::REQUIRED => Constraints\Required::class,
        self::SAMEAS => Constraints\Sameas::class,
    ];

    /**
     * @return class-string<ConstraintInterface>
     * @throws TypeNotFoundException
     */
    public static function getConstraintClassName(string $constraint): string
    {
        return self::CLASS_NAMES[$constraint]
            ?? throw new TypeNotFoundException(sprintf('constraint %s not found', $constraint), 1731340801);
    }
}
