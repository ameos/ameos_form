<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\EventListener;

use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Event\ValidFormEvent;

final class MoveTemporaryFile
{
    public function __invoke(ValidFormEvent $event): void
    {
        /** @var Upload[] $elements */
        $elements = $event->getForm()->getElements();
        foreach ($elements as $element) {
            if (is_a($element, Upload::class) && !empty($element->getValue())) {
                /** @var array */
                $values = $element->getValue();
                $newValues = [];

                foreach ($values as $value) {
                    $temporaryFilepath = $element->getTemporaryDirectory() . $value;
                    $destinationFilepath = $this->getFinalUpdateFilepath($element, $value);

                    rename($temporaryFilepath, $destinationFilepath);

                    $newValues[] = basename($destinationFilepath);
                }
                $element->setValue($newValues);
                $element->updateState(Upload::STATE_DONE);
            }
        }
    }

    /**
     * return final file path
     * @param Upload $element
     * @param string $clientFilename
     * @return string
     */
    private function getFinalUpdateFilepath(Upload $element, string $clientFilename)
    {
        $directory = $element->getUploadDirectory();
        if ($element->getForcedFilename()) {
            return $directory . $element->getForcedFilename();
        }

        if (!file_exists($directory)) {
            mkdir($directory);
        }

        if (file_exists($directory . $clientFilename) && !$element->canOverwrite()) {
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
