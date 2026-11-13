<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository;

use Ameos\AmeosForm\Domain\Repository\Trait\SearchableRepository as SearchableRepositoryTrait;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Base repository for search forms
 *
 * @template T of DomainObjectInterface
 * @extends Repository<T>
 */
class SearchableRepository extends Repository implements SearchableRepositoryInterface
{
    use SearchableRepositoryTrait;
}
