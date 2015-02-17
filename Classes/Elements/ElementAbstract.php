<?php

namespace Ameos\AmeosForm\Elements;

abstract class ElementAbstract implements ElementInterface {

	/**
	 * @var string $name name
	 */
	protected $name;

	/**
	 * @var string $absolutename absolutename
	 */
	protected $absolutename;
	
	/**
	 * @var string $value value
	 */
	protected $value;

	/**
	 * @var bool $valueSetted valueSetted
	 */
	protected $valueSetted = FALSE;
	
	/**
	 * @var array $configuration configuration
	 */
	protected $configuration;

	/**
	 * @var array $errors errors
	 */
	protected $errors = null;

	/**
	 * @var array $systemerror systemerror
	 */
	protected $systemerror = [];
	
	/**
	 * @var array $validators validator
	 */
	protected $validators = [];

	/**
	 * @var \Ameos\AmeosForm\Form $form form
	 */
	protected $form;

	/**
	 * @var bool $searchable searchable
	 */
	protected $searchable = TRUE;

	/**
	 * @var	bool|callable $overrideClause override clause function
	 */
	protected $overrideClause = FALSE;
	
	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) {
		$this->name = $name;
		$this->form = $form;
		$this->configuration = $configuration;
		$this->absolutename  = $absolutename;
		$this->initValue();
	}

	/**
	 * return html attribute
	 * @return string html attribute
	 */
	public function getAttributes() {
		$output = '';
		$output.= isset($this->configuration['placeholder']) ? ' placeholder="' . $this->configuration['placeholder'] . '"' : '';
		$output.= isset($this->configuration['disabled']) && $this->configuration['disabled'] == true ? ' disabled="disabled"' : '';
		$output.= isset($this->configuration['title']) ? ' title="' . $this->configuration['title'] . '"' : '';
		$output.= isset($this->configuration['datalist']) ? ' list="' . $this->getHtmlId() . '-datalist"' : '';

		$cssclass = isset($this->configuration['class']) ? $this->configuration['class'] : '';
		if(!$this->isValid()) $cssclass.= isset($this->configuration['errorclass']) ? ' ' . $this->configuration['errorclass'] : '';
		if($cssclass != '') {
			$output.= ' class="' . $cssclass . '"';
		}
		return $output;
	}

	/**
	 * return html datalist
	 * @return string html datalist
	 */
	public function getDatalist() {
		if(isset($this->configuration['datalist'])) {
			$output = '<datalist id="' . $this->getHtmlId() . '-datalist' . '">';
			if(is_array($this->configuration['datalist'])) {
				foreach($this->configuration['datalist'] as $value => $label) {
					$output.= '<option value="' . $value . '">' . $label . '</option>';
				}
			}
			$output.= '</datalist>';
			return $output;
		}
		return '';
	}

	/**
	 * add configuration
	 *
	 * @param	string	$key configuration key
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function addConfiguration($key, $value) {
		$this->configuration[$key] = $value;
		return $this;
	}

	/**
	 * set the value
	 * 
	 * @param	string	$value value
	 * @return 	\Ameos\AmeosForm\Elements\ElementAbstract this
	 */
	public function setValue($value) {
		$this->valueSetted = TRUE;
		$this->value = $value;

		if($this->form !== FALSE) {
			if($this->form->getMode() == 'crud/extbase') {
				$method = 'set' . \Ameos\AmeosForm\Utility\String::camelCase($this->name);
				if(method_exists($this->form->getModel(), $method)) {
					$this->form->getModel()->$method($value);
				}
			}

			if($this->form->getMode() == 'crud/classic') {
				$this->form->setData($this->name, $value);
			}
		}

		return $this;
	}

	/**
	 * init value from the context
	 */
	protected function initValue() {
		if($this->form !== FALSE) {
			if($this->form->getMode() == 'crud/extbase') {
				$method = 'get' . \Ameos\AmeosForm\Utility\String::camelCase($this->name);
				if(method_exists($this->form->getModel(), $method)) {
					$this->setValue($this->form->getModel()->$method());
				}
			}

			if($this->form->getMode() == 'crud/classic') {
				$this->setValue($this->form->getData($this->name));
			}
		}

		if(!$this->valueSetted && isset($this->configuration['defaultValue'])) {
			$this->setValue($this->configuration['defaultValue']);
		}
		return $this;
	}

	/**
	 * return the value
	 *
	 * @return	string value
	 */
	public function getValue() {
		if($this->valueSetted === TRUE) {
			return $this->value;
		}
		$this->initValue();
		return $this->value;
	}

	/**
	 * return the name
	 *
	 * @return	string name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * return search field
	 *
	 * @return	string search field
	 */
	public function getSearchField() {
		if(isset($this->configuration['searchfield'])) {
			return $this->configuration['searchfield'];
		}
		return $this->name;
	}

	/**
	 * return the name
	 *
	 * @return	string name
	 */
	public function getHtmlId() {
		return str_replace(['.', '[', ']'], ['_', '_', ''], $this->absolutename);
	}

	/**
	 * return where clause
	 *
	 * @return	bool|array FALSE if no search. Else array with search type and value
	 */
	public function getClause() {
		if($this->getValue() != '') {
			if($this->overrideClause !== FALSE) {
				$function = $this->overrideClause;
				$searchInformation = $function($this->getValue(), $this, $this->form);
				$searchInformation['elementname']  = $this->getName();
				$searchInformation['elementvalue'] = $this->getValue();
				return $searchInformation;
			} else {
				return [
					'elementname'  => $this->getName(),
					'elementvalue' => $this->getValue(),
					'field' => $this->getSearchField(),
					'type'  => 'like',
					'value' => '%' . $this->getValue() . '%'
				];
			}
		}
		return FALSE;
	}

	/**
	 * set ovrride clause method
	 * @param	function $overrideClause function
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function setOverrideClause($overrideClause) {
		$this->overrideClause = $overrideClause;
		return $overrideClause;
	}

	/**
	 * add validator
	 * 
	 * @param	\Ameos\AmeosForm\Validators\ValidatorInterface	$validator
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function validator($validator) {
		$this->validators[] = $validator;
		return $this;
	}

	/**
	 * determine errors
	 * 
	 * @return	\Ameos\AmeosForm\Form this
	 */
	public function determineErrors() {
		if($this->errors === null) {
			$this->errors = array();
			if($this->form !== FALSE && $this->form->isSubmitted()) {
				$value = $this->getValue();			
				foreach($this->validators as $validator) {
					if(!$validator->isValid($value)) {
						$this->errors[] = $validator->getMessage();
					}
				}
				foreach($this->systemerror as $error) {				
					$this->errors[] = $error;
				}
			}
		}
		return $this;
	}
	
	/**
	 * return true if the element is valide
	 *
	 * @return	bool true if the element is valide
	 */
	public function isValid() {
		$this->determineErrors();
		return sizeof($this->errors) === 0;
	}

	/**
	 * return errors
	 *
	 * @return	array errors
	 */
	public function  getErrors() {
		$this->determineErrors();
		return $this->errors;
	}

	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = $this->configuration;
		$data['__compiled']   = $this->toHtml();
		$data['name']         = $this->name;
		$data['value']        = $this->getValue();
		$data['absolutename'] = $this->absolutename;
		$data['htmlid']       = $this->getHtmlId();
		$data['errors']       = $this->getErrors();
		$data['isvalid']      = $this->isValid();
		$data['hasError']     = !$this->isValid();
		if(isset($this->configuration['datalist'])) {
			$data['datalist'] = 'datalist';
		}
		return $data;
	}

	/**
	 * return true if element is searchable
	 * 
	 * @return 	bool
	 */
	public function isSearchable() {
		return $this->searchable;
	}
	
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	abstract public function toHtml();
	
	/**
	 * to string
	 */ 
	public function __toString() {
        return (string)$this->toHtml();
    }
}
