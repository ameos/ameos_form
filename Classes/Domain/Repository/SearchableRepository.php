<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Domain\Repository;

use Ameos\AmeosForm\Domain\Repository\Trait\SearchableRepository as TraitSearchableRepository;
use TYPO3\CMS\Extbase\Persistence\Repository;

class SearchableRepository extends Repository implements SearchableRepositoryInterface
{
    use TraitSearchableRepository;
}
