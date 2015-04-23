<?php
namespace Ameos\AmeosForm\Form\Crud;

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

class ClassicForm extends \Ameos\AmeosForm\Form\Crud {
	
	/**
	 * @var int $recordIdentifier
	 */
	protected $recordIdentifier = FALSE;

	/**
	 * @var string $tablename
	 */
	protected $tablename = '';

	/**
	 * @var string $data
	 */
	protected $data = [];

	/**
	 * @constuctor
	 *
	 * @param	string $identifier form identifier
	 * @param	\TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model model
	 */
	public function __construct($identifier, $tablename = '', $recordIdentifier = FALSE) {
		parent::__construct($identifier);
		$this->tablename = $tablename;
		$this->recordIdentifier = $recordIdentifier;
		if($this->tablename != '' && $this->recordIdentifier && $this->recordIdentifier > 0) {
			$resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->tablename, 'uid = ' . $this->recordIdentifier);
			if(($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource)) !== FALSE) {
				$this->data = $record;
			}
		}
		$this->mode = 'crud/classic';
	}

	/**
	 * return datas
	 * 
	 * @return array value
	 */
	public function getDatas() {
		return $this->data;
	}
	
	/**
	 * return data
	 *
	 * @param string $key data key
	 * @return mixed value
	 */
	public function getData($key) {
		if($this->hasData($key)) {
			return $this->data[$key];
		}
		return FALSE;
	}

	/**
	 * set data
	 *
	 * @param string $key data key
	 * @param string $value data value
	 */
	public function setData($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * set datas
	 *
	 * @param array $datas datas
	 */
	public function setDatas($datas) {
		$this->data = $datas;
	}

	/**
	 * return true if data exist
	 *
	 * @param string $key data key
	 * @return bool true if data exist
	 */
	public function hasData($key) {
		return array_key_exists($key, $this->data);
	}

	/**
	 * persist data
	 */
	public function persist() {
		if($this->tablename != '') {
			$dataToPersist = [];
			$fields = $GLOBALS['TYPO3_DB']->admin_get_fields($this->tablename);
			foreach($fields as $field => $fieldInformation) {
				if($this->hasData($field)) {
					$dataToPersist[$field] = $this->getData($field);
				}	
			}
			
			if($this->recordIdentifier && $this->recordIdentifier > 0) {
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tablename, 'uid = ' . $this->recordIdentifier, $dataToPersist);
			} else {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tablename, $dataToPersist);
			}
		}
	}
}
