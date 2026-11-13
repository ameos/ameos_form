<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

/**
 * Configuration: repository (Extbase repository), field (property, defaults to the element name)
 * Invalid if another record than the attached entity has the same value
 */
class Unique extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $repository = $this->configuration['repository'] ?? null;
        if (!$repository instanceof RepositoryInterface) {
            throw new BadConfigurationException('Unique constraint requires a "repository" configuration', 1731340814);
        }
        $field = $this->stringify($this->configuration['field'] ?? '') ?: $this->element->getName();

        $query = $repository->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals($field, $value));

        $attachedEntity = $this->form->getAttachedEntity();
        $attachedUid = $attachedEntity !== null ? self::getUid($attachedEntity) : null;
        foreach ($query->execute() as $record) {
            $uid = $record instanceof DomainObjectInterface ? self::getUid($record) : null;
            if ($attachedUid === null || $uid !== $attachedUid) {
                return false;
            }
        }
        return true;
    }

    private static function getUid(DomainObjectInterface $domainObject): ?int
    {
        return $domainObject->getUid();
    }
}
