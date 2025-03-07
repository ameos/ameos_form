<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository\Trait;

trait SearchableRepository
{
    /**
     * return query clause
     * @param array $clause clause
     * @param object $query query
     * @return object
     */
    protected function getQueryClause($clause, $query)
    {
        if (is_array($clause)) {
            switch ($clause['type']) {
                case 'contains':
                    return $query->$clause['type']($clause['field'], $clause['value']);
                    break;

                case 'logicalOr':
                    if (is_array($clause['clauses'])) {
                        $subclauses = [];
                        foreach ($clause['clauses'] as $subclause) {
                            $subclauses[] = $this->getQueryClause($subclause, $query);
                        }
                        return $query->logicalOr(...$subclauses);
                    }
                    break;

                case 'logicalAnd':
                    if (is_array($clause['clauses'])) {
                        $subclauses = [];
                        foreach ($clause['clauses'] as $subclause) {
                            $subclauses[] = $this->getQueryClause($subclause, $query);
                        }
                        return $query->logicalAnd(...$subclauses);
                    }
                    break;

                case 'logicalNot':
                    return $query->logicalNot($this->getQueryClause($clause['clause'], $query));
                    break;

                default:
                    $type = (string)$clause['type'];
                    return $query->$type($clause['field'], $clause['value']);
                    break;
            }
        }
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
