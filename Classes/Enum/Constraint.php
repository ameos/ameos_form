<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Enum;

class Constraint
{
    public const EMAIL = 'email';
    public const SAMEAS = 'sameas';
    public const UNIQUE = 'unique';
    public const FILEEXTENSION = 'fileextension';
    public const FILEMIME = 'filemime';
    public const FILESIZE = 'filesize';
    public const CUSTOM = 'custom';
    public const REQUIRED = 'required';
}
