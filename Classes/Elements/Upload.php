<?php

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\Events;

class Upload extends ElementAbstract {
	
	/**
	 * @var bool $searchable searchable
	 */
	protected $searchable = FALSE;
	
	/**
	 * @var string $uploadState etat de l'upload ( no-upload, temporary-upload, upload )
	 */
	protected $uploadState = 'no-upload';

	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$output = '';
		if($this->getValue() && !(isset($this->configuration['show_link']) && (bool)$this->configuration['show_link'] === FALSE)) {
			if($this->uploadState == 'temporary-upload') {
				$output .= '<a target="_blank" href="/typo3temp/ameos_form/tempupload/' . $this->getValue() . '">Voir le fichier</a>';
				$output .= '<input type="hidden" value="' . $this->getValue() . '" id="' . $this->getHtmlId() . '-temporary" name="' . $this->absolutename . '[temporary]" />';
				
			} else {
				$output .= '<a target="_blank" href="' . $this->getUploadDirectoryUri() . $this->getValue() . '">Voir le fichier</a>';
			}	
		}
		$output .= '<input type="file" id="' . $this->getHtmlId() . '-upload" name="' . $this->absolutename . '[upload]"' . $this->getAttributes() . ' />';
		return $output;
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = parent::getRenderingInformation();
		if($this->uploadState == 'temporary-upload') {
			$data['filepath'] = '/typo3temp/ameos_form/tempupload/' . $this->getValue();
		} elseif(file_exists($this->getUploadDirectory() . $this->getValue())) {
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
	public function setValue($value) {

		if(is_array($value)) {
			if(isset($value['upload']) && is_array($value['upload']) && $value['upload']['error'] == 0) {
				$this->value = $value;
				$this->determineErrors();
				if($this->isValid()) {
					$directory = $this->getUploadDirectory();
					$filename = $this->getUploadFilename($value['upload']['name']);
					$temporaryFilepath = $this->getTemporaryUpdateFilepath($value['upload']['name']);

					GeneralUtility::upload_copy_move($value['upload']['tmp_name'], $temporaryFilepath);

					Events::getInstance($this->form->getIdentifier())->registerEvent('form_is_valid', [$this, 'moveTemporaryUploadedFile'], [
						'destinationFilepath' => $directory . $filename,
						'temporaryFilepath'   => $temporaryFilepath,
					]);

					$this->uploadState = 'temporary-upload';
			
					parent::setValue(basename($temporaryFilepath));
				} else {
					$this->value = null;
				}
			} elseif(isset($value['temporary'])) {
				$directory = $this->getUploadDirectory();
				$filename = $this->getUploadFilename($value['temporary']);
				
				Events::getInstance($this->form->getIdentifier())->registerEvent('form_is_valid', [$this, 'moveTemporaryUploadedFile'], [
					'destinationFilepath' => $directory . $filename,
					'temporaryFilepath'   => PATH_site . 'typo3temp/ameos_form/tempupload/' . $value['temporary'],
				]);
					
				$this->uploadState = 'temporary-upload';
				
				parent::setValue($value['temporary']);
			}
			
		} else {
			parent::setValue($value);
		}
		
		return $this;		
	}

	/**
	 * Return directory
	 * @return string
	 */
	protected function getUploadDirectory() {
		return PATH_site . trim($this->configuration['directory'], '/') . '/';
	}

	/**
	 * Return uri
	 * @return string
	 */
	protected function getUploadDirectoryUri() {
		return '/' . trim($this->configuration['directory'], '/') . '/';
	}

	/**
	 * Return file name
	 * @param string $clientFilename filename on the client system file
	 * @return string
	 */
	protected function getUploadFilename($clientFilename) {
		if(isset($this->configuration['filename']) && trim((string)$this->configuration['filename']) !== '') {
			return trim((string)$this->configuration['filename']);
		}
		
		$directory = $this->getUploadDirectory();
		if(file_exists($directory . $clientFilename) && !$this->canOverwrite()) {
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
	protected function getTemporaryUpdateFilepath($clientFilename) {
		$directory = PATH_site . 'typo3temp/ameos_form/tempupload/';
		if(file_exists($directory . $clientFilename)) {
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
	protected function canOverwrite() {
		return isset($this->configuration['canoverwrite']) && (bool)$this->configuration['canoverwrite'] === TRUE;
	}

	/**
	 * move temporary uploaded file to final destination
	 * @param string $destinationFilepath destination file path
	 * @param string $destinationFilepath temporary file path
	 */
	public function moveTemporaryUploadedFile($destinationFilepath, $temporaryFilepath) {
		$directory = dirname($destinationFilepath);
		if(!file_exists($directory)) {
			mkdir($directory, 0770, TRUE);
		}

		
		$this->uploadState = 'upload';
		if(isset($this->configuration['fal']) && (bool)$this->configuration['fal'] === TRUE) {
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
