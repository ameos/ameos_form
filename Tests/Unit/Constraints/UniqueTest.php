<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Constraints;

use Ameos\AmeosForm\Constraints\Unique;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ComparisonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

final class UniqueTest extends AbstractUnitTestCase
{
    #[Test]
    public function emptyValueIsValid(): void
    {
        self::assertTrue($this->createConstraint([])->isValid(''));
    }

    #[Test]
    public function noExistingRecordIsValid(): void
    {
        self::assertTrue($this->createConstraint([])->isValid('john@example.com'));
    }

    #[Test]
    public function existingRecordIsNotValidWhenCreating(): void
    {
        self::assertFalse($this->createConstraint([$this->createEntity(5)])->isValid('john@example.com'));
    }

    #[Test]
    public function attachedEntityItselfIsValidWhenUpdating(): void
    {
        self::assertTrue($this->createConstraint([$this->createEntity(5)], $this->createEntity(5))->isValid('john@example.com'));
    }

    #[Test]
    public function otherRecordIsNotValidWhenUpdating(): void
    {
        self::assertFalse($this->createConstraint([$this->createEntity(6)], $this->createEntity(5))->isValid('john@example.com'));
    }

    #[Test]
    public function repositoryIsRequired(): void
    {
        $form = $this->createFormStub();
        $constraint = new Unique('error', [], new Text('tx_test[email]', 'email', [], $form), $form);

        $this->expectException(BadConfigurationException::class);
        $constraint->isValid('john@example.com');
    }

    /**
     * @param list<AbstractEntity> $records
     */
    private function createConstraint(array $records, ?AbstractEntity $attachedEntity = null): Unique
    {
        $querySettings = $this->createMock(QuerySettingsInterface::class);
        $querySettings->expects(self::any())->method('setRespectStoragePage')->with(false)->willReturnSelf();

        $query = $this->createStub(QueryInterface::class);
        $query->method('getQuerySettings')->willReturn($querySettings);
        $query->method('equals')->willReturnCallback(function (string $field, mixed $value): ComparisonInterface {
            self::assertSame('email', $field);
            self::assertSame('john@example.com', $value);
            return $this->createStub(ComparisonInterface::class);
        });
        $query->method('matching')->willReturnSelf();
        $query->method('execute')->willReturn($records);

        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('createQuery')->willReturn($query);

        $form = $this->createStub(Form::class);
        $form->method('getAttachedEntity')->willReturn($attachedEntity);

        return new Unique('error', ['repository' => $repository], new Text('tx_test[email]', 'email', [], $form), $form);
    }

    private function createEntity(int $uid): AbstractEntity
    {
        $entity = new class extends AbstractEntity {};
        $entity->_setProperty('uid', $uid);
        return $entity;
    }
}
