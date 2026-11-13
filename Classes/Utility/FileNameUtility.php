<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Utility;

/**
 * @internal
 */
final class FileNameUtility
{
    /**
     * Return $fileName, or "name_1.ext", "name_2.ext"... if it already exists in $directory
     */
    public static function getAvailableName(string $directory, string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $suffix = $extension !== '' ? '.' . $extension : '';

        $candidate = $fileName;
        for ($index = 1; file_exists($directory . $candidate); $index++) {
            $candidate = $baseName . '_' . $index . $suffix;
        }
        return $candidate;
    }
}
