<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Constraints\Traits\DisableGlobalErrorMessage;
use Ameos\AmeosForm\Constraints\Traits\UploadedFiles;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration: allowed, comma separated list of extensions. Message placeholders: %file_name%, %extensions%
 */
class Fileextension extends ConstraintAbstract
{
    use DisableGlobalErrorMessage;
    use UploadedFiles;

    public function isValid(mixed $value): bool
    {
        $allowed = strtolower($this->stringify($this->configuration['allowed'] ?? ''));
        $valid = true;
        foreach ($this->getFileNames($value) as $fileName) {
            if (!GeneralUtility::inList($allowed, strtolower(pathinfo($fileName, PATHINFO_EXTENSION)))) {
                $this->rejectFile($fileName, [
                    '%file_name%' => $fileName,
                    '%extensions%' => implode(', ', explode(',', $allowed)),
                ]);
                $valid = false;
            }
        }
        return $valid;
    }
}
