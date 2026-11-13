<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository;

interface SearchableRepositoryInterface
{
    /**
     * @param array<array-key, array<string, mixed>> $clauses
     * @return iterable<mixed>
     */
    public function findByClausesArray(array $clauses, ?string $orderby = null, string $direction = 'ASC'): iterable;
}
