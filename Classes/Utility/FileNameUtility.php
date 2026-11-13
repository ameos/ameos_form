<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Utility;

/**
 * @internal
 */
final class FileNameUtility
{
    /**
     * Safe file name from a name given by the client: directory parts are removed, characters other than
     * letters, digits, ".", "_" and "-" are replaced with "_", leading and trailing dots / underscores are
     * removed (no hidden file such as ".htaccess"). Empty string if nothing is left.
     */
    public static function sanitize(string $fileName): string
    {
        $fileName = basename(str_replace('\\', '/', $fileName));
        $fileName = preg_replace('/[^\p{L}\p{N}._-]+/u', '_', $fileName) ?? '';
        $fileName = preg_replace('/\.{2,}/', '.', $fileName) ?? '';
        return trim($fileName, '._');
    }

    /**
     * Return $fileName, or "name_1.ext", "name_2.ext"... if it already exists in $directory
     *
     * @throws \InvalidArgumentException if $fileName is not a plain file name
     */
    public static function getAvailableName(string $directory, string $fileName): string
    {
        if ($fileName === '' || $fileName === '.' || $fileName === '..' || $fileName !== basename(str_replace('\\', '/', $fileName))) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a plain file name', $fileName), 1731340817);
        }
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
