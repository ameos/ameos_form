<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Elements\Traits\Choices;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Checkbox extends ElementAbstract
{
    use Choices;

    /**
     * Stored as comma separated list
     */
    public function setValue(mixed $value): self
    {
        if (is_array($value)) {
            $value = implode(',', array_map($this->stringify(...), $value));
        }
        parent::setValue($value);
        return $this;
    }

    /**
     * @return list<string> checked values
     */
    public function getValue(): mixed
    {
        $value = parent::getValue();
        if (is_array($value)) {
            return array_values(array_map($this->stringify(...), $value));
        }
        return GeneralUtility::trimExplode(',', $this->stringify($value), true);
    }

    public function isOmittedFromRequestWhenEmpty(): bool
    {
        return !$this->isDisabled();
    }

    public function getClause(): array|false
    {
        /** @var list<string> $values */
        $values = $this->getValue();
        if ($values === []) {
            return false;
        }
        if ($this->overrideClause !== null) {
            return $this->buildOverriddenClause();
        }

        $clause = $this->buildClause('logicalOr', null);
        unset($clause['value']);
        $clause['clauses'] = array_map(
            fn(string $value): array => ['field' => $this->getSearchField(), 'type' => $this->getClauseType(), 'value' => $value],
            $values,
        );
        return $clause;
    }

    /**
     * Option "clausetype": "contains" (default, relation field) or "inList" (comma separated values in a string field)
     */
    protected function getClauseType(): string
    {
        $type = $this->configuration['clausetype'] ?? null;
        return is_string($type) && $type !== '' ? $type : 'contains';
    }

    protected function renderSingleInput(string|int $value, string $label): string
    {
        /** @var list<string> $checkedValues */
        $checkedValues = $this->getValue();
        $checked = in_array((string) $value, $checkedValues, true) ? ' checked="checked"' : '';
        return '<input '
            . 'id="' . $this->getHtmlId() . '-' . $this->escapeConfiguration($value) . '" '
            . 'name="' . $this->absolutename . '[]" '
            . 'type="checkbox" '
            . 'value="' . $this->escapeConfiguration($value) . '"'
            . $checked
            . $this->getChoiceAttributes() . ' />';
    }

    protected function getNoItemsErrorMessage(): string
    {
        return 'No items defined for this checkbox';
    }
}
