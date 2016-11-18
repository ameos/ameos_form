<?php
namespace Ameos\AmeosForm\Elements;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\Events;
use Ameos\AmeosForm\Utility\StringUtility;

class Upload extends ElementAbstract 
{
	
	/**
	 * @var bool $searchable searchable
	 */
	protected $searchable = false;
	
	/**
	 * @var string $uploadState etat de l'upload ( no-upload, temporary-upload, upload )
	 */
	protected $uploadState = 'no-upload';
	
	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) 
	{
		parent::__construct($absolutename, $name, $configuration, $form);
		if (isset($this->configuration['directory'])) {
			$this->configuration['directory'] = $this->form->stringUtility->smart($this->configuration['directory']);
		}
	}

	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$multiple = (isset($this->configuration['multiple']) && (bool)$this->configuration['multiple'] === true) ? ' multiple="multiple"' : '';
		
		$output = '';
		if ($this->getValue() && !(isset($this->configuration['show_link']) && (bool)$this->configuration['show_link'] === false)) {
			if ($this->uploadState == 'temporary-upload') {
				$values = GeneralUtility::trimExplode(',', $this->getValue());				
				foreach ($values as $value) {				
					$output.= '<a target="_blank" href="/typo3temp/ameos_form/tempupload/' . $value . '">Voir le fichier ' . $value . '</a> ';
					$output.= '<input type="hidden" value="' . $value . '" id="' . $this->getHtmlId() . '-temporary-' . $value . '" name="' . $this->absolutename . '[temporary][]" />';
				}
				
			} else {
				$values = GeneralUtility::trimExplode(',', $this->getValue());
				foreach ($values as $value) {
					$output.= '<a target="_blank" href="' . $this->getUploadDirectoryUri() . $value . '">Voir le fichier ' . $value . '</a> ';
				}
				
			}	
		}
		$output .= '<input type="file" ' . $multiple . 'id="' . $this->getHtmlId() . '-upload" name="' . $this->absolutename . '[upload][]"' . $this->getAttributes() . ' />';
		return $output;
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() 
	{
		$data = parent::getRenderingInformation();
		if ($this->uploadState == 'temporary-upload') {
			$data['filepath'] = '/typo3temp/ameos_form/tempupload/' . $this->getValue();
		} elseif (file_exists($this->getUploadDirectory() . $this->getValue())) {
			$data['filepath'] = $this->getUploadDirectoryUri() . $this->getValue();
		}

		return $data;
	}

	/**
	 * set the value
	 *
	 * @todo : fal mode
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) 
	{
		if (is_array($value)) {
			if (isset($value['upload']) && is_array($value['upload']) && $value['upload'][0]['name'] != '') {
				$this->value = $value;
				$this->determineErrors();
				$currentValue = [];
				if ($this->isValid()) {
					foreach ($value['upload'] as $uploadFile) {
						$directory = $this->getUploadDirectory();
						$filename = $this->getUploadFilename($uploadFile['name']);
						$temporaryFilepath = $this->getTemporaryUpdateFilepath($uploadFile['name']);
					
						GeneralUtility::upload_copy_move($uploadFile['tmp_name'], $temporaryFilepath);
					
						Events::getInstance($this->form->getIdentifier())->registerEvent('form_is_valid', [$this, 'moveTemporaryUploadedFile'], [
								'destinationFilepath' => $directory . $filename,
								'temporaryFilepath'   => $temporaryFilepath,
						]);
					
						$this->uploadState = 'temporary-upload';
						
						$currentValue[] = basename($temporaryFilepath);
						
					}
					
					parent::setValue($currentValue);
				} else {
					$this->value = null;
				}
				
			} elseif (isset($value['temporary'])) {
				$currentValue = [];
				foreach ($value['temporary'] as $uploadFile) {
					$directory = $this->getUploadDirectory();
					$filename = $this->getUploadFilename($value['temporary']);
					
					Events::getInstance($this->form->getIdentifier())->registerEvent('form_is_valid', [$this, 'moveTemporaryUploadedFile'], [
						'destinationFilepath' => $directory . $filename,
						'temporaryFilepath'   => PATH_site . 'typo3temp/ameos_form/tempupload/' . $value['temporary'],
					]);
						
					$this->uploadState = 'temporary-upload';
					
					$currentValue[] = basename($temporaryFilepath);
				}
				parent::setValue($currentValue);
			}
			
		} else {
			if (is_string($this->value) && $this->value != '') {
				$value = $this->value . ',' . $value;
			}
			parent::setValue($value);
		}
		
		return $this;		
	}

	/**
	 * determine errors
	 * 
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function determineErrors() 
	{
		if ($this->elementConstraintsAreChecked === false) {
			if ($this->form !== FALSE && $this->form->isSubmitted()) {
				$values = $this->getValue();

                // check required constrainnt
                if (!$values) {
                    foreach ($this->constraints as $constraint) {
                        if (is_a($constraint, 'Ameos\\AmeosForm\\Constraints\\Required')) {
                            if (!$constraint->isValid($values)) {
                                $this->form->getErrorManager()->add($constraint->getMessage(), $this);
                            }
                        }
                    }
                }

                // check other constraints
                if (isset($values['upload']) && is_array($values['upload'])) {
                    foreach ($values['upload'] as $value) {
                        foreach ($this->constraints as $constraint) {
                            if (!is_a($constraint, 'Ameos\\AmeosForm\\Constraints\\Required')) {
                                if (!$constraint->isValid($value)) {
                                    $this->form->getErrorManager()->add($constraint->getMessage(), $this);
                                }
                            }
                        }
                    }
                }
				foreach ($this->systemerror as $error) {				
					$this->form->getErrorManager()->add($error, $this);
				}
				$this->elementConstraintsAreChecked = TRUE;
			}
		}
		return $this;
	}

	/**
	 * Return directory
	 * @return string
	 */
	protected function getUploadDirectory() 
	{
		return PATH_site . trim($this->configuration['directory'], '/') . '/';
	}

	/**
	 * Return uri
	 * @return string
	 */
	protected function getUploadDirectoryUri() 
	{
		return '/' . trim($this->configuration['directory'], '/') . '/';
	}

	/**
	 * Return file name
	 * @param string $clientFilename filename on the client system file
	 * @return string
	 */
	protected function getUploadFilename($clientFilename) 
	{
		if (isset($this->configuration['filename']) && trim((string)$this->configuration['filename']) !== '') {
			return trim((string)$this->configuration['filename']);
		}
		
		$directory = $this->getUploadDirectory();
		if (file_exists($directory . $clientFilename) && !$this->canOverwrite()) {
			$fileIndex = 1;
			do {
				$fileExtension = substr($clientFilename, strrpos($clientFilename, '.') + 1);
				$filename = basename($clientFilename, '.' . $fileExtension) . '_' . $fileIndex . '.' . $fileExtension;
				$fileIndex++;
			} while(file_exists($directory . $filename));
		} else {
			$filename = $clientFilename;
		}
		
		return $filename;
	}

	/**
	 * return temporary file path
	 * @param string $clientFilename filename on the client system file
	 * @return string
	 */
	protected function getTemporaryUpdateFilepath($clientFilename) 
	{
		$directory = PATH_site . 'typo3temp/ameos_form/tempupload/';
		if (file_exists($directory . $clientFilename)) {
			$fileIndex = 1;
			do {
				$fileExtension = substr($clientFilename, strrpos($clientFilename, '.') + 1);
				$filename = basename($clientFilename, '.' . $fileExtension) . '_' . $fileIndex . '.' . $fileExtension;
				$fileIndex++;
			} while(file_exists($directory . $filename));
		} else {
			$filename = $clientFilename;
		}
		return $directory . $filename;
	}

	/**
	 * return true if upload can overwrite existing file
	 * @return bool
	 */
	protected function canOverwrite() 
	{
		return isset($this->configuration['canoverwrite']) && (bool)$this->configuration['canoverwrite'] === true;
	}

	/**
	 * move temporary uploaded file to final destination
	 * @param string $destinationFilepath destination file path
	 * @param string $destinationFilepath temporary file path
	 */
	public function moveTemporaryUploadedFile($destinationFilepath, $temporaryFilepath) 
	{
		$directory = dirname($destinationFilepath);
		if (!file_exists($directory)) {
			mkdir($directory, 0770, true);
		}

		
		$this->uploadState = 'upload';
		if (isset($this->configuration['fal']) && (bool)$this->configuration['fal'] === true) {
			$storageIdentifier = isset($this->configuration['storageIdentifier']) ? (int)$this->configuration['storageIdentifier'] : 1;
			$storageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository'); 
			$storage = $storageRepository->findByUid($storageIdentifier);
			$fileObject = $storage->addFile($temporaryFilepath, $storage->getRootLevelFolder(), $destinationFilepath);
		//debug($destinationFilepath);
		//debug($fileObject->getUid());

		} else {
			rename($temporaryFilepath, $destinationFilepath);
			$this->setValue(basename($destinationFilepath));	
		}
	}
}
