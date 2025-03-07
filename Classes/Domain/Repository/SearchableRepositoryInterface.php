<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository;

interface SearchableRepositoryInterface
{
    /**
     * Return query result
     *
     * @param   array $clauses where clauses
     * @param   string $orderby order by
     * @param   string $direction direction
     * @return  iterable
     */
    public function findByClausesArray($clauses, $orderby = null, $direction = 'ASC');
}
