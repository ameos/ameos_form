<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * File upload. Files are first stored in a temporary directory outside the public path (var/transient/), then
 * moved to "directory" when the form is valid. Pending files are never served by the web server.
 * Options: directory (required, relative to the public path), multiple, maxSize (e.g. "5M"), canoverwrite,
 * filename (forced file name), show_link
 */
class Upload extends ElementAbstract
{
    public const string STATE_EMPTY = 'empty';
    public const string STATE_PENDING = 'pending';
    public const string STATE_DONE = 'done';

    /** Relative to the var path (not public) */
    protected const string TEMPORARY_PATH = 'transient/ameos_form/upload/';
    /** Temporary files older than this (abandoned forms) are deleted */
    public const int TEMPORARY_FILE_LIFETIME = 86400;
    /** Temporary files are named "<16 hex chars>_<original name>", the random part cannot be guessed */
    protected const string TEMPORARY_NAME_PATTERN = '/^[0-9a-f]{16}_(.+)$/';

    protected bool $searchable = false;
    protected string $uploadState = self::STATE_EMPTY;

    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (!is_string($this->configuration['directory'] ?? null) || $this->configuration['directory'] === '') {
            throw new BadConfigurationException('Directory is missing', 1731340809);
        }
        if (!is_dir($this->getTemporaryDirectory())) {
            GeneralUtility::mkdir_deep($this->getTemporaryDirectory());
        }
    }

    /**
     * @return list<string> file names
     */
    public function getFileNames(): array
    {
        $value = $this->getValue();
        $values = is_array($value) ? $value : [$value];
        return array_values(array_filter(array_map($this->stringify(...), $values), static fn(string $file): bool => $file !== ''));
    }

    public function removeFileFromValue(string $fileName): void
    {
        if (is_array($this->value)) {
            $this->value = array_values(array_filter($this->value, static fn(mixed $file): bool => $file !== $fileName));
        }
    }

    public function showLink(): bool
    {
        return (bool) ($this->configuration['show_link'] ?? true);
    }

    public function isMultiple(): bool
    {
        return (bool) ($this->configuration['multiple'] ?? false);
    }

    public function canOverwrite(): bool
    {
        return (bool) ($this->configuration['canoverwrite'] ?? false);
    }

    public function getUploadDirectory(bool $absolute = true): string
    {
        return ($absolute ? Environment::getPublicPath() . '/' : '') . $this->getRelativeUploadDirectory();
    }

    public function getTemporaryDirectory(): string
    {
        return Environment::getVarPath() . '/' . self::TEMPORARY_PATH;
    }

    /**
     * Delete the temporary files of abandoned forms
     */
    public function removeExpiredTemporaryFiles(int $lifetime = self::TEMPORARY_FILE_LIFETIME): void
    {
        $limit = time() - $lifetime;
        foreach (glob($this->getTemporaryDirectory() . '*') ?: [] as $file) {
            if (is_file($file) && filemtime($file) < $limit) {
                @unlink($file);
            }
        }
    }

    /**
     * Unique and unguessable name for a new temporary file
     */
    public function createTemporaryFileName(string $originalFileName): string
    {
        return bin2hex(random_bytes(8)) . '_' . $originalFileName;
    }

    /**
     * Original name of a temporary file (the name itself for other files)
     */
    public function getOriginalFileName(string $fileName): string
    {
        return preg_match(self::TEMPORARY_NAME_PATTERN, $fileName, $matches) === 1 ? $matches[1] : $fileName;
    }

    /**
     * Delete a temporary file (e.g. rejected by a constraint)
     */
    public function deleteTemporaryFile(string $fileName): void
    {
        if ($this->filterTemporaryFileNames([$fileName]) !== []) {
            unlink($this->getTemporaryDirectory() . $fileName);
        }
    }

    /**
     * Keep only temporary file names (random prefix) that exist in the temporary directory.
     * Protects against path traversal and against the use of temporary files uploaded by other visitors.
     *
     * @param array<array-key, mixed> $fileNames
     * @return list<string>
     */
    public function filterTemporaryFileNames(array $fileNames): array
    {
        $validFileNames = [];
        foreach ($fileNames as $fileName) {
            $fileName = $this->stringify($fileName);
            if ($fileName === basename($fileName)
                && preg_match(self::TEMPORARY_NAME_PATTERN, $fileName) === 1
                && is_file($this->getTemporaryDirectory() . $fileName)
            ) {
                $validFileNames[] = $fileName;
            }
        }
        return $validFileNames;
    }

    public function getForcedFilename(): string|false
    {
        $filename = $this->stringify($this->configuration['filename'] ?? '');
        return $filename !== '' ? basename($filename) : false;
    }

    public function updateState(string $state): self
    {
        $this->uploadState = $state;
        return $this;
    }

    public function getState(): string
    {
        return $this->uploadState;
    }

    public function toHtml(): string
    {
        $output = '';
        foreach ($this->getFileNames() as $fileName) {
            $output .= $this->renderFile($fileName);
        }

        $output .= '<input type="file" ' . ($this->isMultiple() ? ' multiple="multiple" ' : '')
            . 'id="' . $this->getHtmlId() . '-upload" '
            . 'name="' . $this->absolutename . '[upload][]"' . $this->getAttributes() . ' />';
        $resetLabel = $this->translate('reset-upload');
        $output .= '<button type="button" id="' . $this->getHtmlId() . '-uploadreset" '
            . 'class="ameos-form-elementupload-resetbutton" title="' . $resetLabel . '">'
            . '<span class="ameos-form-elementupload-resetbutton-inner">' . $resetLabel . '</span></button>';

        $this->assetCollector->addJavaScript('ameos-form-elementupload', 'EXT:ameos_form/Resources/Public/Elements/upload.js');

        return $output;
    }

    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        $filePaths = [];
        $pendingFiles = [];
        foreach ($this->getFileNames() as $fileName) {
            if ($this->uploadState === self::STATE_PENDING) {
                // not validated yet, not public: only the name is available
                $pendingFiles[] = $this->getOriginalFileName($fileName);
            } elseif (is_file($this->getUploadDirectory() . $fileName)) {
                $filePaths[] = $this->getUploadDirectoryUri() . rawurlencode($fileName);
            }
        }
        if ($pendingFiles !== []) {
            $data['pendingfiles'] = $pendingFiles;
        }
        if ($filePaths !== []) {
            $data['filepath'] = $filePaths[0];
            $data['filepaths'] = $filePaths;
        }
        return $data;
    }

    protected function renderFile(string $fileName): string
    {
        if ($this->uploadState === self::STATE_PENDING) {
            return $this->renderTemporaryFile($fileName);
        }
        if ($this->showLink()) {
            return $this->renderViewLink($this->getUploadDirectoryUri() . rawurlencode($fileName), $fileName, '');
        }
        return '';
    }

    protected function renderTemporaryFile(string $fileName): string
    {
        $id = $this->getHtmlId() . '-temporary-' . $this->escape($fileName);
        $deleteLabel = $this->translate('delete-file');
        // pending files are stored outside the public path: name only, no link
        return '<span id="' . $id . '-viewlink" class="ameos-form-elementupload-pendingfile">'
            . $this->escape($this->getOriginalFileName($fileName)) . '</span> '
            . '<button type="button" id="' . $id . '-deletebutton" class="ameos-form-elementupload-deletebutton" '
            . 'title="' . $deleteLabel . '&nbsp;: ' . $this->escape($this->getOriginalFileName($fileName)) . '">'
            . '<span class="ameos-form-elementupload-deletebutton-inner">' . $deleteLabel . '</span></button>'
            . '<input type="hidden" value="' . $this->escape($fileName) . '" id="' . $id . '" '
            . 'name="' . $this->absolutename . '[temporary][]" />';
    }

    protected function renderViewLink(string $uri, string $fileName, string $id): string
    {
        return '<a ' . ($id !== '' ? 'id="' . $id . '" ' : '') . 'target="_blank" href="' . $this->escape($uri) . '" '
            . 'class="ameos-form-elementupload-viewlink">'
            . '<span class="ameos-form-elementupload-viewlink-inner">' . $this->translate('view-file')
            . '&nbsp;: <span class="ameos-form-elementupload-viewlink-filename">' . $this->escape($fileName)
            . '</span></span></a> ';
    }

    protected function getUploadDirectoryUri(): string
    {
        return '/' . $this->getRelativeUploadDirectory();
    }

    protected function translate(string $key): string
    {
        return LocalizationUtility::translate($key, 'ameos_form', null, null, $this->form->getCurrentRequest()) ?? $key;
    }

    private function getRelativeUploadDirectory(): string
    {
        return trim($this->stringify($this->configuration['directory'] ?? ''), '/') . '/';
    }
}
