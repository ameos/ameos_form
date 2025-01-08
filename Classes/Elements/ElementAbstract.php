<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Constraints\ConstraintInterface;
use Ameos\AmeosForm\Constraints\Required;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class ElementAbstract implements ElementInterface
{
    /**
     * @var AssetCollector
     */
    protected $assetCollector;

    /**
     * @var string $name name
     */
    protected $name;

    /**
     * @var string $absolutename absolutename
     */
    protected $absolutename;

    /**
     * @var mixed $value value
     */
    protected $value;

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
     * @var array $constraints constraints
     */
    protected $constraints = [];

    /**
     * @var Form $form form
     */
    protected $form;

    /**
     * @var bool $searchable searchable
     */
    protected $searchable = true;

    /**
     * @var    bool|callable $overrideClause override clause function
     */
    protected $overrideClause = false;

    /**
     * @var bool $isVerified true if element constraints are checked
     */
    protected $isVerified = false;

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
        $this->assetCollector = GeneralUtility::makeInstance(AssetCollector::class);

        $this->name = $name;
        $this->form = $form;
        $this->configuration = $configuration;
        $this->absolutename  = $absolutename;
    }

    /**
     * return html attribute
     *
     * @return string
     */
    public function getAttributes(): string
    {
        $output = '';
        $output .= isset($this->configuration['placeholder']) ? ' placeholder="' . $this->configuration['placeholder'] . '"' : '';
        $output .= isset($this->configuration['style']) ? ' style="' . $this->configuration['style'] . '"' : '';
        $output .= isset($this->configuration['disabled']) && $this->configuration['disabled'] == true ? ' disabled="disabled"' : '';
        $output .= isset($this->configuration['title']) ? ' title="' . $this->configuration['title'] . '"' : '';
        $output .= isset($this->configuration['datalist']) ? ' list="' . $this->getHtmlId() . '-datalist"' : '';
        $output .= isset($this->configuration['type']) ? ' type="' . $this->configuration['type'] . '"' : '';
        $output .= isset($this->configuration['custom']) ? ' ' . $this->configuration['custom'] : '';

        $cssclass = isset($this->configuration['class']) ? $this->configuration['class'] : '';
        if (!$this->isValid()) {
            $cssclass .= isset($this->configuration['errorclass']) ? ' ' . $this->configuration['errorclass'] : '';
        }
        if ($cssclass != '') {
            $output .= ' class="' . $cssclass . '"';
        }
        return $output;
    }

    /**
     * return html datalist
     *
     * @return string
     */
    public function getDatalist(): string
    {
        if (isset($this->configuration['datalist']) && is_array($this->configuration['datalist'])) {
            $output = '<datalist id="' . $this->getHtmlId() . '-datalist">';
            foreach ($this->configuration['datalist'] as $value => $label) {
                $output .= '<option value="' . $value . '" label="' . $label . '">' . $label . '</option>';
            }
            $output .= '</datalist>';
            return $output;
        }
        return '';
    }

    /**
     * add configuration
     *
     * alias addConfiguration
     * @param string $key configuration key
     * @param mixed $value value
     * @return self
     */
    public function with(string $key, mixed $value): self
    {
        return $this->addConfiguration($key, $value);
    }

    /**
     * add configuration
     *
     * @param string $key configuration key
     * @param mixed $value value
     * @return self
     */
    public function addConfiguration(string $key, mixed $value): self
    {
        $this->configuration[$key] = $value;

        return $this;
    }

    /**
     * set the value
     *
     * @param mixed $value value
     * @return self
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;
        $this->form->updateEntityProperty($this->name, $value);

        return $this;
    }

    /**
     * return the value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        if (is_null($this->value)) {
            return isset($this->configuration['defaultValue']) ? $this->configuration['defaultValue'] : '';
        }
        return $this->value;
    }

    /**
     * return the name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * return the configuration
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * return search field
     *
     * @return string
     */
    public function getSearchField(): string
    {
        if (isset($this->configuration['searchfield'])) {
            return $this->configuration['searchfield'];
        }
        return $this->name;
    }

    /**
     * return the html id
     *
     * @return string
     */
    public function getHtmlId(): string
    {
        return str_replace(['.', '[', ']'], ['_', '_', ''], $this->absolutename);
    }

    /**
     * return where clause
     *
     * @return array|false
     */
    public function getClause(): array|false
    {
        if (!empty($this->getValue())) {
            if ($this->overrideClause !== false) {
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
        return false;
    }

    /**
     * set ovrride clause method
     *
     * @param \Closure $overrideClause function
     * @return self
     */
    public function setOverrideClause(\Closure $overrideClause): self
    {
        $this->overrideClause = $overrideClause;

        return $this;
    }

    /**
     * add validator
     *
     * @param ConstraintInterface $constraint
     * @return self
     * @alias    addConstraint
     */
    public function validator(ConstraintInterface $constraint): self
    {
        return $this->addConstraint($constraint);
    }

    /**
     * add constraint
     *
     * @param ConstraintInterface $constraint
     * @return self
     */
    public function addConstraint(ConstraintInterface $constraint): self
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * determine errors
     *
     * @return self
     */
    public function determineErrors(): self
    {
        if ($this->isVerified === false) {
            if ($this->form !== false && $this->form->isSubmitted()) {
                $value = $this->getValue();
                foreach ($this->constraints as $constraint) {
                    if (!$constraint->isValid($value)) {
                        $errorMessage = $constraint->getMessage();
                        if ($errorMessage !== null) {
                            $this->form->getErrorManager()->add($errorMessage, $this->getName());
                        }
                    }
                }
                foreach ($this->systemerror as $error) {
                    $this->form->getErrorManager()->add($error, $this->getName());
                }
                $this->isVerified = true;
            }
        }
        return $this;
    }

    /**
     * return true if the element is valide
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->form->getErrorManager()->elementIsValid($this);
    }

    /**
     * return errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->form->getErrorManager()->getErrorsFor($this);
    }

    /**
     * return true if the element is valide
     *
     * @return bool
     */
    public function getIsRequired(): bool
    {
        foreach ($this->constraints as $constraint) {
            if (is_a($constraint, Required::class)) {
                return true;
            }
        }
        return false;
    }

    /**
     * return true if the element is valide
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->getIsRequired();
    }

    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array
    {
        $data = $this->configuration;
        $data['__compiled']   = $this->toHtml();
        $data['name']         = $this->name;
        $data['value']        = $this->getValue();
        $data['absolutename'] = $this->absolutename;
        $data['htmlid']       = $this->getHtmlId();
        $data['errors']       = $this->getErrors();
        $data['isvalid']      = $this->isValid();
        $data['required']     = $this->isRequired();
        $data['hasError']     = !$this->isValid();
        if (isset($this->configuration['datalist'])) {
            $data['datalist'] = 'datalist';
        }
        return $data;
    }

    /**
     * return true if element is searchable
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * form to html
     *
     * @return string
     */
    abstract public function toHtml(): string;

    /**
     * to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->toHtml();
    }
}
