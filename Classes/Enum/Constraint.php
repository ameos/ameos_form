<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Enum;

use Ameos\AmeosForm\Constraints;
use Ameos\AmeosForm\Exception\TypeNotFoundException;

enum Constraint: string
{
    public const EMAIL = 'email';
    public const SAMEAS = 'sameas';
    public const UNIQUE = 'unique';
    public const FILEEXTENSION = 'fileextension';
    public const FILEMIME = 'filemime';
    public const FILESIZE = 'filesize';
    public const CUSTOM = 'custom';
    public const REQUIRED = 'required';

    public static function getConstraintClassName(string $constraint): string
    {
        $className = '';
        switch ($constraint) {
            case self::EMAIL:
                $className = Constraints\Email::class;
                break;
            case self::UNIQUE:
                $className = Constraints\Unique::class;
                break;
            case self::FILEEXTENSION:
                $className = Constraints\Fileextension::class;
                break;
            case self::FILEMIME:
                $className = Constraints\Filemime::class;
                break;
            case self::CUSTOM:
                $className = Constraints\Custom::class;
                break;
            case self::REQUIRED:
                $className = Constraints\Required::class;
                break;
            case self::SAMEAS:
                $className = Constraints\Sameas::class;
                break;
            default:
                throw new TypeNotFoundException(sprintf('constraint %s not found', $constraint));
        }
        return $className;
    }
}
