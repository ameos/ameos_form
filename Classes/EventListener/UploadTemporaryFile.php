<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\EventListener;

use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use Ameos\AmeosForm\Utility\FileNameUtility;
use Psr\Http\Message\UploadedFileInterface;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Store the uploaded files in the temporary directory. When no file is uploaded, keep the already uploaded
 * (temporary) files. Option "maxSize" (default "5M", units K/KO, M/MO, G/GO, or bytes)
 */
#[AsEventListener(identifier: 'uploadTemporaryFile')]
final readonly class UploadTemporaryFile
{
    private const string DEFAULT_MAX_SIZE = '5M';
    private const array UNITS = ['K' => 1024, 'M' => 1024 ** 2, 'G' => 1024 ** 3];

    public function __construct(private FileNameValidator $fileNameValidator) {}

    public function __invoke(BindValueFromRequestEvent $event): void
    {
        $element = $event->getElement();
        $value = $event->getValue();
        if (!$element instanceof Upload || !is_array($value)) {
            return;
        }

        $uploadedFiles = $this->getSuccessfulUploads($value['upload'] ?? null);
        $temporaryFiles = $value['temporary'] ?? null;

        $fileNames = $uploadedFiles !== []
            ? $this->storeUploadedFiles($event, $element, $uploadedFiles)
            : $element->filterTemporaryFileNames(is_array($temporaryFiles) ? $temporaryFiles : []);

        if ($fileNames !== []) {
            $element->updateState(Upload::STATE_PENDING);
        }
        $event->setValue($fileNames);
    }

    /**
     * @return list<UploadedFileInterface>
     */
    private function getSuccessfulUploads(mixed $uploads): array
    {
        $uploadedFiles = [];
        foreach (is_array($uploads) ? $uploads : [] as $file) {
            if ($file instanceof UploadedFileInterface && $file->getError() === UPLOAD_ERR_OK) {
                $uploadedFiles[] = $file;
            }
        }
        return $uploadedFiles;
    }

    /**
     * @param list<UploadedFileInterface> $uploadedFiles
     * @return list<string> temporary file names
     */
    private function storeUploadedFiles(BindValueFromRequestEvent $event, Upload $element, array $uploadedFiles): array
    {
        $configuredMaxSize = $element->getConfigurationItem('maxSize');
        $maxSize = is_scalar($configuredMaxSize) ? (string) $configuredMaxSize : self::DEFAULT_MAX_SIZE;
        $maxBytes = $this->convertMaxSize($maxSize);

        $element->removeExpiredTemporaryFiles();
        $fileNames = [];
        foreach ($uploadedFiles as $uploadedFile) {
            $clientFileName = basename(str_replace('\\', '/', (string) $uploadedFile->getClientFilename()));
            $fileName = FileNameUtility::sanitize($clientFileName);
            $error = match (true) {
                (int) $uploadedFile->getSize() > $maxBytes => 'fileSizeError',
                $fileName === '' || !$this->fileNameValidator->isValid($fileName) => 'fileNameError',
                default => null,
            };
            if ($error !== null) {
                $this->addError($event, $element, $error, ['%file_name%' => $clientFileName, '%max_size%' => $maxSize]);
                continue;
            }
            $fileName = $element->createTemporaryFileName($fileName);
            $uploadedFile->moveTo($element->getTemporaryDirectory() . $fileName);
            $fileNames[] = $fileName;
        }
        return $fileNames;
    }

    /**
     * Convert "5M", "500Ko", "1G"... to bytes, a plain number is a size in bytes
     */
    private function convertMaxSize(string $maxSize): int
    {
        if (preg_match('/^\s*(\d+)\s*([KMG])?O?\s*$/i', $maxSize, $matches) !== 1) {
            return 0;
        }
        $unit = strtoupper($matches[2] ?? '');
        return (int) $matches[1] * (self::UNITS[$unit] ?? 1);
    }

    /**
     * @param array<string, string> $replacements
     */
    private function addError(BindValueFromRequestEvent $event, Upload $element, string $key, array $replacements): void
    {
        $form = $event->getForm();
        $message = LocalizationUtility::translate($key, 'ameos_form', null, null, $form->getCurrentRequest()) ?? $key;
        $form->getErrorManager()->add(strtr($message, $replacements), $element->getName());
    }
}
