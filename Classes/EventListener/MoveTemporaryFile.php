<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\EventListener;

use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Event\ValidFormEvent;
use Ameos\AmeosForm\Utility\FileNameUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Move the uploaded files from the temporary directory to the element directory once the form is valid
 */
#[AsEventListener(identifier: 'moveTemporaryFile')]
final class MoveTemporaryFile
{
    public function __invoke(ValidFormEvent $event): void
    {
        foreach ($event->getForm()->getElements() as $element) {
            if ($element instanceof Upload && $element->getState() === Upload::STATE_PENDING) {
                $this->moveFiles($element);
            }
        }
    }

    private function moveFiles(Upload $element): void
    {
        $directory = $element->getUploadDirectory();
        if (!is_dir($directory)) {
            GeneralUtility::mkdir_deep($directory);
        }

        $movedFiles = [];
        foreach ($element->filterTemporaryFileNames($element->getFileNames()) as $fileName) {
            $destination = $this->getDestinationPath($element, $directory, $element->getOriginalFileName($fileName));
            if (rename($element->getTemporaryDirectory() . $fileName, $destination)) {
                GeneralUtility::fixPermissions($destination);
                $movedFiles[] = basename($destination);
            }
        }

        $element->setValue($movedFiles);
        $element->updateState(Upload::STATE_DONE);
    }

    private function getDestinationPath(Upload $element, string $directory, string $fileName): string
    {
        $forcedFilename = $element->getForcedFilename();
        if ($forcedFilename !== false) {
            return $directory . $forcedFilename;
        }
        return $element->canOverwrite() ? $directory . $fileName : $directory . FileNameUtility::getAvailableName($directory, $fileName);
    }
}
