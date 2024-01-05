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

            if (isset($value['upload']) && is_array($value['upload'])) {
                foreach ($value['upload'] as $uploadFile) {
                    /** @var UploadedFile $uploadFile */

                    $temporaryFilepath = $this->getTemporaryUpdateFilepath($element, $uploadFile->getClientFilename());                    
                    $uploadFile->moveTo($temporaryFilepath);
                    $newValue[] = basename($temporaryFilepath);
                }
                $element->updateState(Upload::STATE_PENDING);
            } elseif (isset($value['temporary']) && is_array($value['temporary'])) {
                $newValue = $value['temporary'];
            }

            $event->setValue($newValue);
        }
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
