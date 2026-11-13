<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Domain;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Domain\Repository\Trait\SearchableRepository;
use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\AndInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ComparisonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\NotInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class SearchableRepositoryTest extends AbstractUnitTestCase
{
    #[Test]
    public function clausesAreConvertedToQueryConstraints(): void
    {
        $calls = [];
        $comparison = $this->createStub(ComparisonInterface::class);
        $query = $this->createMock(QueryInterface::class);
        foreach (['equals', 'like', 'contains', 'in', 'lessThan', 'lessThanOrEqual', 'greaterThan', 'greaterThanOrEqual'] as $method) {
            $query->method($method)->willReturnCallback(static function (string $field, mixed $value) use (&$calls, $method, $comparison): ComparisonInterface {
                $calls[] = [$method, $field, $value];
                return $comparison;
            });
        }
        $query->method('logicalOr')->willReturn($this->createStub(OrInterface::class));
        $query->method('logicalNot')->willReturn($this->createStub(NotInterface::class));
        $query->expects(self::exactly(2))->method('logicalAnd')->willReturn($this->createStub(AndInterface::class));
        $query->expects(self::once())->method('matching')->willReturnSelf();
        $query->expects(self::once())->method('setOrderings')->with(['title' => 'DESC'])->willReturnSelf();
        $result = $this->createStub(QueryResultInterface::class);
        $query->method('execute')->willReturn($result);

        $repository = $this->createRepository($query);
        $actual = $repository->findByClausesArray([
            ['type' => 'like', 'field' => 'title', 'value' => '%foo%', 'elementname' => 'title'],
            ['type' => 'logicalOr', 'clauses' => [
                ['field' => 'tags', 'type' => 'contains', 'value' => 'a'],
                ['field' => 'tags', 'type' => 'contains', 'value' => 'b'],
            ]],
            ['type' => 'logicalAnd', 'clauses' => [['type' => 'greaterThan', 'field' => 'date', 'value' => 1]]],
            ['type' => 'logicalNot', 'clause' => ['type' => 'equals', 'field' => 'hidden', 'value' => 1]],
            ['type' => 'in', 'field' => 'uid', 'value' => [1, 2]],
            ['type' => 'lessThan', 'field' => 'a', 'value' => 1],
            ['type' => 'lessThanOrEqual', 'field' => 'b', 'value' => 2],
            ['type' => 'greaterThanOrEqual', 'field' => 'c', 'value' => 3],
        ], 'title', 'DESC');

        self::assertSame($result, $actual);
        self::assertSame([
            ['like', 'title', '%foo%'],
            ['contains', 'tags', 'a'],
            ['contains', 'tags', 'b'],
            ['greaterThan', 'date', 1],
            ['equals', 'hidden', 1],
            ['in', 'uid', [1, 2]],
            ['lessThan', 'a', 1],
            ['lessThanOrEqual', 'b', 2],
            ['greaterThanOrEqual', 'c', 3],
        ], $calls);
    }

    #[Test]
    public function noClauseMeansNoConstraint(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $query->expects(self::never())->method('matching');
        $query->expects(self::never())->method('setOrderings');
        $query->method('execute')->willReturn([]);

        self::assertSame([], $this->createRepository($query)->findByClausesArray([]));
    }

    #[Test]
    public function unsupportedClauseTypeThrowsException(): void
    {
        $this->expectException(BadConfigurationException::class);
        $this->createRepository($this->createStub(QueryInterface::class))->findByClausesArray([['type' => 'delete', 'field' => 'uid', 'value' => 1]]);
    }

    /**
     * @param QueryInterface<DomainObjectInterface> $query
     */
    private function createRepository(QueryInterface $query): SearchableRepositoryInterface
    {
        /** @extends Repository<DomainObjectInterface> */
        return new class ($query) extends Repository implements SearchableRepositoryInterface {
            use SearchableRepository;

            /**
             * @param QueryInterface<DomainObjectInterface> $query
             */
            public function __construct(private readonly QueryInterface $query) {}

            /**
             * @return QueryInterface<DomainObjectInterface>
             */
            public function createQuery(): QueryInterface
            {
                return $this->query;
            }
        };
    }
}
