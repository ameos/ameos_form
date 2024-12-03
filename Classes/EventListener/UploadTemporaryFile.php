<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\EventListener;

use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use TYPO3\CMS\Core\Http\UploadedFile;

final class UploadTemporaryFile
{
    public function __invoke(BindValueFromRequestEvent $event): void
    {
        $element = $event->getElement();
        if (is_a($element, Upload::class)) {
            /** @var Upload $element */
            /** @var array */
            $value = $event->getValue();
            $newValue = [];
            $uploadedFiles = [];

            $elementConfiguration = $element->getConfiguration();
            $maxSize = array_key_exists('maxSize', $elementConfiguration) ? $elementConfiguration['maxSize'] : '5M';
            $maxSizeValue = $this->convertMaxSize($maxSize);

            if (isset($value['upload']) && is_array($value['upload'])) {
                foreach ($value['upload'] as $uploadedFile) {
                    /** @var UploadedFile $uploadedFile */
                    
                    if ($this->isFileSizeValid($uploadedFile, $maxSizeValue)) {
                        $fileName = $uploadedFile->getClientFilename();
                        $temporaryFilepath = $this->getTemporaryUpdateFilepath(
                            $element,
                            $fileName
                        );                    
                        $uploadedFile->moveTo($temporaryFilepath);
                        $newValue[] = basename($temporaryFilepath);
                    } else {
                        $event->getForm()->getErrorManager()->add(
                            'La taille du fichier "' . $fileName . '" excéde la limite autorisée de ' . $maxSize . '.',
                            $element->getName()
                        );
                    }
                }

                if (!empty($newValue)) {
                    $element->updateState(Upload::STATE_PENDING);
                }
            } elseif (isset($value['temporary']) && is_array($value['temporary'])) {
                $newValue = $value['temporary'];
            }

            $event->setValue($newValue);
        }
    }

    /**
     * return converted string size in bytes
     * @param string $maxSizeStr
     * @return int
     */
    private function convertMaxSize(string $maxSizeStr): int
    {
        $intValue = intval($maxSizeStr);
        $oneLetter = strtoupper(substr($maxSizeStr, -1));
        $twoLetters = strtoupper(substr($maxSizeStr, -2));
        if ($oneLetter == 'K' || $twoLetters == 'KO') {
            $maxSize = $intValue * 1024;
        } else if ($oneLetter == 'M' || $twoLetters == 'MO') {
            $maxSize = $intValue * 1024 * 1024;
        } else if ($oneLetter == 'G' || $twoLetters == 'GO'){
            $maxSize = $intValue * 1024 * 1024 * 1024;
        }

        return $maxSize;
    }

    /**
     * return if the size is valid for an uploaded file
     * @param UploadFile $uploadedFile
     * @param int $maxSize
     * @return bool
     */
    private function isFileSizeValid(UploadedFile $uploadedFile, int $maxSize): bool
    {
        $fileSize = $uploadedFile->getSize();
        return $fileSize <= $maxSize;
    }

    /**
     * return temporary file path
     * @param Upload $element
     * @param string $clientFilename
     * @return string
     */
    private function getTemporaryUpdateFilepath(Upload $element, string $clientFilename)
    {
        $directory = $element->getTemporaryDirectory();
        if (file_exists($directory . $clientFilename)) {
            $fileIndex = 1;
            do {
                $fileExtension = substr($clientFilename, strrpos($clientFilename, '.') + 1);
                $filename = basename($clientFilename, '.' . $fileExtension) . '_' . $fileIndex . '.' . $fileExtension;
                $fileIndex++;
            } while (file_exists($directory . $filename));
        } else {
            $filename = $clientFilename;
        }
        return $directory . $filename;
    }
}
