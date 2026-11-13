<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints\Traits;

use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Exception\BadConfigurationException;

/**
 * Shared by constraints checking the files of an upload element
 *
 * @phpstan-require-extends \Ameos\AmeosForm\Constraints\ConstraintAbstract
 */
trait UploadedFiles
{
    protected function getUploadElement(): Upload
    {
        if (!$this->element instanceof Upload) {
            throw new BadConfigurationException(static::class . ' can only be used on upload elements', 1731340810);
        }
        return $this->element;
    }

    /**
     * @return list<string>
     */
    protected function getFileNames(mixed $value): array
    {
        $values = is_array($value) ? $value : [$value];
        return array_values(array_filter(array_map($this->stringify(...), $values), static fn(string $file): bool => $file !== ''));
    }

    /**
     * Remove the file from the element, delete it if it is a temporary file and add an error message.
     * %file_name% is replaced with the original file name.
     *
     * @param array<string, string> $replacements
     */
    protected function rejectFile(string $fileName, array $replacements): void
    {
        $element = $this->getUploadElement();
        $element->removeFileFromValue($fileName);
        $element->deleteTemporaryFile($fileName);
        $replacements['%file_name%'] = $element->getOriginalFileName($fileName);
        $this->form->getErrorManager()->add(strtr($this->message, $replacements), $element->getName());
    }
}
