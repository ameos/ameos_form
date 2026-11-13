<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit;

use Ameos\AmeosForm\Constraints\Fileextension;
use Ameos\AmeosForm\Constraints\Filemime;
use Ameos\AmeosForm\Constraints\Required;
use Ameos\AmeosForm\Constraints\Sameas;
use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Domain\Repository\Trait\SearchableRepository;
use Ameos\AmeosForm\Elements\Checkbox;
use Ameos\AmeosForm\Elements\Date;
use Ameos\AmeosForm\Elements\Number;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\Elements\Textdate;
use Ameos\AmeosForm\Elements\Time;
use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\AndInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ComparisonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Bugs found while porting to TYPO3 14
 */
final class RegressionTest extends AbstractUnitTestCase
{
    private string $previousTimezone;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousTimezone = date_default_timezone_get();
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->previousTimezone);
        parent::tearDown();
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function timeDataProvider(): iterable
    {
        yield 'empty parts' => [['hour' => '', 'minute' => ''], ''];
        yield 'padded' => [['hour' => '9', 'minute' => '5'], '09:05'];
        yield 'minute missing' => [['hour' => '14', 'minute' => ''], '14:00'];
        yield 'list' => [['8', '30'], '08:30'];
        yield 'string' => ['10:15', '10:15'];
    }

    #[Test]
    #[DataProvider('timeDataProvider')]
    public function timeValueIsNormalized(mixed $value, string $expected): void
    {
        $element = new Time('tx_test[t]', 't', [], $this->createFormStub());
        $element->setValue($value);

        self::assertSame($expected, $element->getValue());
    }

    #[Test]
    public function emptyTimeIsRequired(): void
    {
        $form = $this->createFormStub();
        $element = new Time('tx_test[t]', 't', [], $form);
        $element->setValue(['hour' => '', 'minute' => '']);

        self::assertFalse((new Required('error', [], $element, $form))->isValid($element->getValue()));
    }

    #[Test]
    public function emptyTextdateIsRenderedEmpty(): void
    {
        $element = new Textdate('tx_test[d]', 'd', [], $this->createFormStub());

        self::assertStringContainsString('value=""', $element->toHtml());
    }

    #[Test]
    public function timestampsUseTheDefaultTimezone(): void
    {
        date_default_timezone_set('Europe/Paris');
        // 2024-01-01 00:00 in Paris is 2023-12-31 23:00 UTC
        $timestamp = (new \DateTime('2024-01-01 00:00:00'))->getTimestamp();
        $element = new Textdate('tx_test[d]', 'd', [], $this->createFormStub());
        $element->setValue($timestamp);

        self::assertStringContainsString('value="2024-01-01"', $element->toHtml());
        $value = $element->getValue();
        self::assertInstanceOf(\DateTimeInterface::class, $value);
        self::assertSame('2024-01-01', $value->format('Y-m-d'));
    }

    #[Test]
    public function requiredAcceptsTimestampDates(): void
    {
        $form = $this->createFormStub();
        $element = new Date('tx_test[d]', 'd', ['input' => 'timestamp'], $form);
        $element->setValue('2024-05-01');
        $constraint = new Required('error', [], $element, $form);

        self::assertIsInt($element->getValue());
        self::assertTrue($constraint->isValid($element->getValue()));
        self::assertFalse($constraint->isValid(''));
    }

    #[Test]
    public function sameasComparesStrictly(): void
    {
        $form = $this->createStub(Form::class);
        $password = new Text('tx_test[p]', 'p', [], $form);
        $form->method('getElement')->willReturn($password);
        $constraint = new Sameas('error', ['sameas' => 'p'], new Text('tx_test[c]', 'c', [], $form), $form);

        $password->setValue('1000');
        self::assertFalse($constraint->isValid('1e3'));
        self::assertTrue($constraint->isValid('1000'));
        $password->setValue(1000);
        self::assertTrue($constraint->isValid('1000'));
    }

    #[Test]
    public function zeroAttributesAreRendered(): void
    {
        $element = new Number('tx_test[n]', 'n', ['min' => 0, 'max' => '0', 'step' => '', 'disabled' => false], $this->createFormStub());

        self::assertSame('<input type="number" id="tx_test_n" name="tx_test[n]" value="" min="0" max="0" />', $element->toHtml());
    }

    #[Test]
    public function checkboxInputsHaveTheElementAttributes(): void
    {
        $element = new Checkbox('tx_test[c]', 'c', ['items' => ['a' => 'A'], 'class' => 'check', 'disabled' => true], $this->createFormStub());

        self::assertStringContainsString('value="a" class="check" disabled />', $element->toHtml());
    }

    #[Test]
    public function checkboxClauseTypeCanBeChanged(): void
    {
        $element = new Checkbox('tx_test[c]', 'c', ['items' => ['a' => 'A'], 'clausetype' => 'inList'], $this->createFormStub());
        $element->setValue(['a']);

        $clause = $element->getClause();
        self::assertIsArray($clause);
        self::assertSame([['field' => 'c', 'type' => 'inList', 'value' => 'a']], $clause['clauses']);
    }

    #[Test]
    public function rejectedTemporaryFileIsDeletedAndOriginalNameIsDisplayed(): void
    {
        $form = $this->createFormStub(true);
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin'], $form);
        $fileName = $element->createTemporaryFileName('evil.html');
        $path = $element->getTemporaryDirectory() . $fileName;
        file_put_contents($path, '<script>alert(1)</script>');
        $element->setValue([$fileName]);
        $element->updateState(Upload::STATE_PENDING);
        $element->addConstraint(new Fileextension('"%file_name%" not allowed', ['allowed' => 'pdf'], $element, $form));
        $element->addConstraint(new Filemime('"%file_name%" bad mime', [], $element, $form));

        $element->determineErrors();

        self::assertFileDoesNotExist($path);
        self::assertSame([], $element->getFileNames());
        // the second constraint gets the value without the rejected file: one message only
        self::assertSame(['"evil.html" not allowed'], $form->getErrorManager()->getErrorsFor($element));
    }

    #[Test]
    public function inListClauseMatchesCommaSeparatedValues(): void
    {
        $calls = [];
        $query = $this->createStub(QueryInterface::class);
        foreach (['equals', 'like'] as $method) {
            $query->method($method)->willReturnCallback(function (string $field, mixed $value) use (&$calls, $method): ComparisonInterface {
                $calls[] = [$method, $field, $value];
                return $this->createStub(ComparisonInterface::class);
            });
        }
        $query->method('logicalOr')->willReturn($this->createStub(OrInterface::class));
        $query->method('logicalAnd')->willReturn($this->createStub(AndInterface::class));
        $query->method('matching')->willReturnSelf();
        $query->method('execute')->willReturn([]);

        $this->createRepository($query)->findByClausesArray([['type' => 'inList', 'field' => 'tags', 'value' => 'a_b']]);

        self::assertSame([
            ['equals', 'tags', 'a_b'],
            ['like', 'tags', 'a\\_b,%'],
            ['like', 'tags', '%,a\\_b'],
            ['like', 'tags', '%,a\\_b,%'],
        ], $calls);
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
