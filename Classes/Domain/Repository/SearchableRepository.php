<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class SearchableRepository extends Repository
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
     * @param   array   $clauses where clauses
     * @param   bool|string $orderby order by
     * @param   string  $direction direction
     * @return  object
     */
    public function findByClausesArray($clauses, $orderby = false, $direction = 'ASC')
    {
        $query = $this->createQuery();
        $objectsClauses = [];
        foreach ($clauses as $clause) {
            $objectsClauses[] = $this->getQueryClause($clause, $query);
        }
        if (!empty($objectsClauses)) {
            $query->matching($query->logicalAnd(...$objectsClauses));
        }

        if ($orderby !== false) {
            $query->setOrderings(array($orderby => $direction));
        }

        return $query->execute();
    }
}
