<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository\Trait;

trait SearchableRepository
{
    /**
     * return query clause
     * @param ?array $clause clause
     * @param object $query query
     * @return ?object
     */
    protected function getQueryClause(?array $clause, $query)
    {
        $queryClause  = null;
        switch ($clause['type']) {
            case 'contains':
                $queryClause = $query->$clause['type']($clause['field'], $clause['value']);
                break;

            case 'logicalOr':
                if (is_array($clause['clauses'])) {
                    $subclauses = [];
                    foreach ($clause['clauses'] as $subclause) {
                        $subclauses[] = $this->getQueryClause($subclause, $query);
                    }
                    $queryClause = $query->logicalOr(...$subclauses);
                }
                break;

            case 'logicalAnd':
                if (is_array($clause['clauses'])) {
                    $subclauses = [];
                    foreach ($clause['clauses'] as $subclause) {
                        $subclauses[] = $this->getQueryClause($subclause, $query);
                    }
                    $queryClause = $query->logicalAnd(...$subclauses);
                }
                break;

            case 'logicalNot':
                $queryClause = $query->logicalNot($this->getQueryClause($clause['clause'], $query));
                break;

            default:
                $type = (string)$clause['type'];
                $queryClause = $query->$type($clause['field'], $clause['value']);
                break;
        }
        return $queryClause;
    }

    /**
     * Return query result
     *
     * @param   array $clauses where clauses
     * @param   string $orderby order by
     * @param   string $direction direction
     * @return  iterable
     */
    public function findByClausesArray($clauses, $orderby = null, $direction = 'ASC')
    {
        $query = $this->createQuery();
        $objectsClauses = [];
        foreach ($clauses as $clause) {
            $objectsClauses[] = $this->getQueryClause($clause, $query);
        }
        if (!empty($objectsClauses)) {
            $query->matching($query->logicalAnd(...$objectsClauses));
        }

        if ($orderby !== null) {
            $query->setOrderings(array($orderby => $direction));
        }

        return $query->execute();
    }
}
