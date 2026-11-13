<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository\Trait;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Implementation of SearchableRepositoryInterface for Extbase repositories
 *
 * Clause format: ['type' => 'equals|like|contains|in|inList|lessThan|lessThanOrEqual|greaterThan|greaterThanOrEqual',
 * 'field' => ..., 'value' => ...],
 * "contains" is for relations (Extbase), "inList" for comma separated values stored in a string field.
 *
 * ['type' => 'logicalAnd|logicalOr', 'clauses' => [...]] or ['type' => 'logicalNot', 'clause' => [...]]
 *
 * @phpstan-require-extends \TYPO3\CMS\Extbase\Persistence\Repository
 */
trait SearchableRepository
{
    /**
     * @param array<array-key, array<string, mixed>> $clauses
     * @return iterable<mixed>
     */
    public function findByClausesArray(array $clauses, ?string $orderby = null, string $direction = 'ASC'): iterable
    {
        $query = $this->createQuery();
        $constraints = $this->getQueryClauses($clauses, $query);
        if ($constraints !== []) {
            $query->matching($query->logicalAnd(...$constraints));
        }
        if ($orderby !== null) {
            $query->setOrderings([$orderby => $direction]);
        }
        return $query->execute();
    }

    /**
     * @template TEntity of object
     * @param array<array-key, mixed> $clause
     * @param QueryInterface<TEntity> $query
     */
    protected function getQueryClause(array $clause, QueryInterface $query): ConstraintInterface
    {
        $type = is_string($clause['type'] ?? null) ? $clause['type'] : '';
        $field = is_string($clause['field'] ?? null) ? $clause['field'] : '';
        $value = $clause['value'] ?? null;

        return match ($type) {
            'logicalAnd' => $query->logicalAnd(...$this->getQueryClauses($this->getSubClauses($clause), $query)),
            'logicalOr' => $query->logicalOr(...$this->getQueryClauses($this->getSubClauses($clause), $query)),
            'logicalNot' => $query->logicalNot($this->getQueryClause($this->getArray($clause['clause'] ?? null), $query)),
            'equals' => $query->equals($field, $value),
            'like' => $query->like($field, is_string($value) ? $value : ''),
            'contains' => $query->contains($field, $value),
            'in' => $query->in($field, $value),
            'inList' => $this->getInListConstraint($query, $field, $value),
            'lessThan' => $query->lessThan($field, $value),
            'lessThanOrEqual' => $query->lessThanOrEqual($field, $value),
            'greaterThan' => $query->greaterThan($field, $value),
            'greaterThanOrEqual' => $query->greaterThanOrEqual($field, $value),
            default => throw new BadConfigurationException(sprintf('Unsupported clause type "%s"', $type), 1731340815),
        };
    }

    /**
     * The comma separated list stored in $field contains $value
     *
     * @template TEntity of object
     * @param QueryInterface<TEntity> $query
     */
    private function getInListConstraint(QueryInterface $query, string $field, mixed $value): ConstraintInterface
    {
        $value = is_scalar($value) ? (string) $value : '';
        $escaped = addcslashes($value, '%_\\');
        return $query->logicalOr(
            $query->equals($field, $value),
            $query->like($field, $escaped . ',%'),
            $query->like($field, '%,' . $escaped),
            $query->like($field, '%,' . $escaped . ',%'),
        );
    }

    /**
     * @template TEntity of object
     * @param array<array-key, mixed> $clauses
     * @param QueryInterface<TEntity> $query
     * @return list<ConstraintInterface>
     */
    private function getQueryClauses(array $clauses, QueryInterface $query): array
    {
        $constraints = [];
        foreach ($clauses as $clause) {
            $constraints[] = $this->getQueryClause($this->getArray($clause), $query);
        }
        return $constraints;
    }

    /**
     * @param array<array-key, mixed> $clause
     * @return array<array-key, mixed>
     */
    private function getSubClauses(array $clause): array
    {
        return $this->getArray($clause['clauses'] ?? null);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getArray(mixed $value): array
    {
        if (!is_array($value)) {
            throw new BadConfigurationException('Invalid search clause', 1731340816);
        }
        return $value;
    }
}
