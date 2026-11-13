<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Constraints\ConstraintInterface;
use Ameos\AmeosForm\Constraints\Required;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;

abstract class ElementAbstract implements ElementInterface, \Stringable
{
    protected AssetCollector $assetCollector;
    protected mixed $value = null;
    /** @var array<string, mixed> */
    protected array $configuration;
    /** @var list<string> errors added by the element itself */
    protected array $systemerror = [];
    /** @var list<ConstraintInterface> */
    protected array $constraints = [];
    protected bool $searchable = true;
    protected ?\Closure $overrideClause = null;
    /** @var bool true if element constraints are checked */
    protected bool $isVerified = false;

    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(
        protected string $absolutename,
        protected string $name,
        ?array $configuration,
        protected Form $form,
    ) {
        $this->configuration = $configuration ?? [];
        $this->assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
    }

    abstract public function toHtml(): string;

    public function getAttribute(string $key, string $type = 'text'): string
    {
        $configuration = $this->getConfigurationItem($key);
        // 0 is a valid value (e.g. min="0"), only empty values are skipped (and false values for boolean attributes)
        if ($configuration === null || $configuration === '' || $configuration === false || ($type === 'bool' && !$configuration)) {
            return '';
        }
        return $type === 'bool' ? ' ' . $key : ' ' . $key . '="' . $this->escapeConfiguration($configuration) . '"';
    }

    public function getClassAttribute(): string
    {
        $cssclass = $this->stringify($this->configuration['class'] ?? '');
        if (isset($this->configuration['errorclass']) && !$this->isValid()) {
            $cssclass .= ' ' . $this->stringify($this->configuration['errorclass']);
        }
        return $cssclass !== '' ? ' class="' . $this->escapeConfiguration($cssclass) . '"' : '';
    }

    /**
     * Raw attributes string (e.g. 'data-foo="bar"'), output unescaped on purpose
     */
    public function getCustomAttribute(): string
    {
        $configuration = $this->getConfigurationItem('custom');
        return $configuration ? ' ' . $this->stringify($configuration) : '';
    }

    public function getAttributes(): string
    {
        $output = $this->getAttribute('placeholder')
            . $this->getAttribute('style')
            . $this->getAttribute('disabled', 'bool')
            . $this->getAttribute('title')
            . $this->getAttribute('type')
            . $this->getClassAttribute()
            . $this->getCustomAttribute();

        if (isset($this->configuration['datalist'])) {
            $output .= ' list="' . $this->getHtmlId() . '-datalist"';
        }
        return $output;
    }

    public function getDatalist(): string
    {
        $datalist = $this->configuration['datalist'] ?? null;
        if (!is_array($datalist)) {
            return '';
        }
        $output = '<datalist id="' . $this->getHtmlId() . '-datalist">';
        foreach ($datalist as $value => $label) {
            $label = $this->escapeConfiguration($label);
            $output .= '<option value="' . $this->escapeConfiguration($value) . '" label="' . $label . '">' . $label . '</option>';
        }
        return $output . '</datalist>';
    }

    public function with(string $key, mixed $value): self
    {
        return $this->addConfiguration($key, $value);
    }

    public function addConfiguration(string $key, mixed $value): self
    {
        $this->configuration[$key] = $value;
        return $this;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;
        $this->form->updateEntityProperty($this->name, $value);
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value ?? $this->configuration['defaultValue'] ?? '';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getConfigurationItem(string $key): mixed
    {
        return $this->configuration[$key] ?? null;
    }

    public function getSearchField(): string
    {
        $searchField = $this->configuration['searchfield'] ?? null;
        return is_string($searchField) && $searchField !== '' ? $searchField : $this->name;
    }

    public function getHtmlId(): string
    {
        return str_replace(['.', '[', ']'], ['_', '_', ''], $this->absolutename);
    }

    public function getAbsoluteName(): string
    {
        return $this->absolutename;
    }

    public function getClause(): array|false
    {
        if ($this->isEmptyValue($this->getValue())) {
            return false;
        }
        return $this->overrideClause !== null
            ? $this->buildOverriddenClause()
            : $this->buildClause('like', '%' . $this->stringify($this->getValue()) . '%');
    }

    /**
     * Set a closure building the search clause: fn(mixed $value, ElementInterface $element, Form $form): array
     */
    public function setOverrideClause(\Closure $overrideClause): self
    {
        $this->overrideClause = $overrideClause;
        return $this;
    }

    /**
     * Alias of addConstraint()
     */
    public function validator(ConstraintInterface $constraint): self
    {
        return $this->addConstraint($constraint);
    }

    public function addConstraint(ConstraintInterface $constraint): self
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    public function determineErrors(): self
    {
        if ($this->isVerified || !$this->form->isSubmitted()) {
            return $this;
        }

        $errorManager = $this->form->getErrorManager();
        foreach ($this->constraints as $constraint) {
            // value read for each constraint: upload constraints remove the rejected files from the value
            $message = $constraint->isValid($this->getValue()) ? null : $constraint->getMessage();
            if ($message !== null) {
                $errorManager->add($message, $this->getName());
            }
        }
        foreach ($this->systemerror as $error) {
            $errorManager->add($error, $this->getName());
        }
        $this->isVerified = true;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->form->getErrorManager()->elementIsValid($this);
    }

    public function getErrors(): array
    {
        return $this->form->getErrorManager()->getErrorsFor($this);
    }

    public function getIsRequired(): bool
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint instanceof Required) {
                return true;
            }
        }
        return false;
    }

    public function isRequired(): bool
    {
        return $this->getIsRequired();
    }

    public function getRenderingInformation(): array
    {
        $data = $this->configuration;
        $data['__compiled'] = $this->toHtml();
        $data['name'] = $this->name;
        $data['value'] = $this->getValue();
        $data['absolutename'] = $this->absolutename;
        $data['htmlid'] = $this->getHtmlId();
        $data['errors'] = $this->getErrors();
        $data['isvalid'] = $this->isValid();
        $data['required'] = $this->isRequired();
        $data['hasError'] = !$data['isvalid'];
        return $data;
    }

    /**
     * TRUE if the browser does not send the field when it is empty (e.g. unchecked checkbox):
     * the element is then emptied when the form is submitted, instead of keeping the entity value.
     * Disabled elements are never sent, they are not emptied.
     */
    public function isOmittedFromRequestWhenEmpty(): bool
    {
        return false;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildClause(string $type, mixed $value): array
    {
        return [
            'elementname' => $this->getName(),
            'elementvalue' => $this->getValue(),
            'field' => $this->getSearchField(),
            'type' => $type,
            'value' => $value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildOverriddenClause(): array
    {
        $clause = $this->overrideClause !== null
            ? ($this->overrideClause)($this->getValue(), $this, $this->form)
            : [];
        $clause = is_array($clause) ? $clause : [];
        $clause['elementname'] = $this->getName();
        $clause['elementvalue'] = $this->getValue();
        /** @var array<string, mixed> $clause */
        return $clause;
    }

    protected function isEmptyValue(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [] || $value === false;
    }

    protected function isDisabled(): bool
    {
        return (bool) ($this->configuration['disabled'] ?? false);
    }

    /**
     * Convert a scalar or Stringable value to string, anything else to an empty string
     */
    protected function stringify(mixed $value): string
    {
        return match (true) {
            is_string($value) => $value,
            is_int($value), is_float($value) => (string) $value,
            is_bool($value) => $value ? '1' : '',
            $value instanceof \Stringable => (string) $value,
            default => '',
        };
    }

    /**
     * Escape user input for HTML output
     */
    protected function escape(mixed $value): string
    {
        return htmlspecialchars($this->stringify($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escape developer configuration for HTML attributes, existing entities are kept
     */
    protected function escapeConfiguration(mixed $value): string
    {
        return htmlspecialchars($this->stringify($value), ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
    }

    /**
     * Value used for the "value" attribute
     */
    protected function getEscapedValue(): string
    {
        return $this->escape($this->getValue());
    }

    /**
     * Read a property of an object through its getter
     */
    protected function readProperty(mixed $object, string $property): mixed
    {
        $getter = is_object($object) ? [$object, 'get' . ucfirst($property)] : null;
        return $getter !== null && is_callable($getter) ? $getter() : null;
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function getItems(string $errorMessage): array
    {
        $items = $this->configuration['items'] ?? null;
        if (!is_array($items)) {
            throw new BadConstraintException($errorMessage, 1731340804);
        }
        return $items;
    }
}
