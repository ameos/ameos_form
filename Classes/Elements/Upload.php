<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\StorageRepository;

class Upload extends ElementAbstract
{
    public const STATE_EMPTY = 'empty';
    public const STATE_PENDING = 'pending';
    public const STATE_DONE = 'done';

    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @var string
     */
    protected $uploadState = self::STATE_EMPTY;

    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (isset($this->configuration['directory'])) {
            $this->configuration['directory'] = $this->configuration['directory'];
        }
        if (!file_exists(Environment::getPublicPath() . '/typo3temp/ameos_form/tempupload/')) {
            GeneralUtility::mkdir_deep(Environment::getPublicPath() . '/typo3temp/ameos_form/tempupload/');
        }
    }

    /**
     * return true if show link
     * @return bool
     */
    public function showLink(): bool
    {
        return !isset($this->configuration['show_link']) || (bool)$this->configuration['show_link'] === true;
    }

    /**
     * return true if is a multiple upload
     * @return bool
     */
    public function isMultiple(): bool
    {
        return isset($this->configuration['multiple']) && (bool)$this->configuration['multiple'] === true;
    }

    /**
     * return true if upload can overwrite existing file
     * @return bool
     */
    public function canOverwrite(): bool
    {
        return isset($this->configuration['canoverwrite']) && (bool)$this->configuration['canoverwrite'] === true;
    }

    /**
     * Return directory
     * @return string
     */
    public function getUploadDirectory(): string
    {
        return Environment::getPublicPath() . '/' . trim($this->configuration['directory'], '/') . '/';
    }

    /**
     * Return directory
     * @return string
     */
    public function getTemporaryDirectory(): string
    {
        return Environment::getPublicPath() . '/typo3temp/ameos_form/tempupload/';
    }

    /**
     * return forced filename or false
     * 
     * @return string|false
     */
    public function getForcedFilename(): string|false
    {
        return isset($this->configuration['filename']) ? $this->configuration['filename'] : false;
    }

    /**
     * update upload state
     *
     * @param string $state
     * @return self
     */
    public function updateState(string $state): self
    {
        $this->uploadState = $state;

        return $this;
    }

    /**
     * form to html
     *
     * @return    string the html
     */
    public function toHtml(): string
    {
        $multiple = $this->isMultiple() ? ' multiple="multiple"' : '';

        $output = '';

        /** @var array */
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = [$values];
        }

        if ($this->uploadState == self::STATE_PENDING) {
            foreach ($values as $value) {
                $output .= '<a target="_blank" href="/typo3temp/ameos_form/tempupload/' . $value . '">Voir le fichier ' . $value . '</a> ';
                $output .= '<input type="hidden" value="' . $value . '" id="' . $this->getHtmlId() . '-temporary-' . $value . '" name="' . $this->absolutename . '[temporary][]" />';
            }
        } elseif ($this->showLink()) {
            foreach ($values as $value) {
                $output .= '<a target="_blank" href="' . $this->getUploadDirectoryUri() . $value . '">Voir le fichier ' . $value . '</a> ';
            }
        }

        $output .= '<input type="file" ' . $multiple . 'id="' . $this->getHtmlId() . '-upload" name="' . $this->absolutename . '[upload][]"' . $this->getAttributes() . ' />';
        return $output;
    }

    /**
     * return rendering information
     *
     * @return    array rendering information
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        if ($this->uploadState == self::STATE_PENDING) {
            $data['filepath'] = '/typo3temp/ameos_form/tempupload/' . $this->getValue();
        } elseif (file_exists($this->getUploadDirectory() . $this->getValue())) {
            $data['filepath'] = $this->getUploadDirectoryUri() . $this->getValue();
        }

        return $data;
    }

    /**
     * Return uri
     * @return string
     */
    protected function getUploadDirectoryUri()
    {
        return '/' . trim($this->configuration['directory'], '/') . '/';
    }
}
