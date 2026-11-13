<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Date;
use Ameos\AmeosForm\Elements\Textdate;
use Ameos\AmeosForm\Elements\Time;
use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateTimeTest extends AbstractUnitTestCase
{
    /**
     * @return iterable<string, array{mixed, string|null}>
     */
    public static function dateValuesDataProvider(): iterable
    {
        yield 'Y-m-d string' => ['2024-03-15', '2024-03-15'];
        yield 'timestamp' => [1710460800, '2024-03-15'];
        yield 'numeric string' => ['1710460800', '2024-03-15'];
        yield 'choice array' => [['year' => '2024', 'month' => '3', 'day' => '15'], '2024-03-15'];
        yield 'date object' => [new \DateTimeImmutable('2024-03-15'), '2024-03-15'];
        yield 'invalid string' => ['not a date', null];
        yield 'invalid array' => [['year' => '2024', 'month' => '2', 'day' => '31'], null];
        yield 'empty string' => ['', null];
    }

    #[Test]
    #[DataProvider('dateValuesDataProvider')]
    public function dateValueIsConverted(mixed $input, ?string $expected): void
    {
        $element = new Date('tx_test[d]', 'd', [], $this->createFormStub());
        $element->setValue($input);
        $value = $element->getValue();

        if ($expected === null) {
            self::assertSame('', $value);
            return;
        }
        self::assertInstanceOf(\DateTimeInterface::class, $value);
        self::assertSame($expected, $value->format('Y-m-d'));
    }

    #[Test]
    public function timestampInputStoresTimestamp(): void
    {
        $element = new Date('tx_test[d]', 'd', ['input' => 'timestamp'], $this->createFormStub());
        $element->setValue('2024-03-15');

        self::assertSame((new \DateTime('2024-03-15'))->getTimestamp(), $element->getValue());
    }

    #[Test]
    public function choiceWidgetFollowsDisplayFormat(): void
    {
        $element = new Date('tx_test[d]', 'd', ['format-display' => 'dmy'], $this->createFormStub());
        $element->setValue('2024-03-15');
        $html = $element->toHtml();

        self::assertLessThan(strpos($html, 'tx_test_d-month'), strpos($html, 'tx_test_d-day'));
        self::assertLessThan(strpos($html, 'tx_test_d-year'), strpos($html, 'tx_test_d-month'));
        self::assertStringContainsString('<option value="15" selected="selected">15</option>', $html);
        self::assertStringContainsString('<option value="2024" selected="selected">2024</option>', $html);

        $data = $element->getRenderingInformation();
        self::assertArrayHasKey('day', $data);
        self::assertArrayHasKey('month', $data);
        self::assertArrayHasKey('year', $data);
    }

    #[Test]
    public function textdateRendersValidDateInput(): void
    {
        $element = new Textdate('tx_test[d]', 'd', [], $this->createFormStub());
        $element->setValue('2024-03-15');

        self::assertSame('<input type="date" id="tx_test_d" name="tx_test[d]" value="2024-03-15" />', $element->toHtml());
    }

    #[Test]
    public function invalidWidgetThrowsException(): void
    {
        $this->expectException(BadConfigurationException::class);
        new Date('tx_test[d]', 'd', ['widget' => 'foo'], $this->createFormStub());
    }

    #[Test]
    public function timeValueIsJoined(): void
    {
        $element = new Time('tx_test[t]', 't', [], $this->createFormStub());
        $element->setValue(['hour' => '09', 'minute' => '30']);

        self::assertSame('09:30', $element->getValue());
        self::assertStringContainsString('name="tx_test[t][hour]" value="09"', $element->toHtml());
        self::assertStringContainsString('name="tx_test[t][minute]" value="30"', $element->toHtml());
    }

    #[Test]
    public function timeMinuteStepIsApplied(): void
    {
        $element = new Time('tx_test[t]', 't', ['minutestep' => 15], $this->createFormStub());

        $minuteOptions = explode('-minute-datalist">', $element->toHtml())[1];

        self::assertSame(4, substr_count($minuteOptions, '<option'));
        self::assertStringContainsString('<option value="45">45</option>', $minuteOptions);
    }

    #[Test]
    public function timeMinuteStepZeroDoesNotLoopForever(): void
    {
        $element = new Time('tx_test[t]', 't', ['minutestep' => 0], $this->createFormStub());

        self::assertSame(60, substr_count(explode('-minute-datalist">', $element->toHtml())[1], '<option'));
    }

    #[Test]
    public function timeTextWidgetRendersTimeInput(): void
    {
        $element = new Time('tx_test[t]', 't', ['widget' => 'text'], $this->createFormStub());
        $element->setValue('10:15');

        self::assertSame('<input type="time" id="tx_test_t" name="tx_test[t]" value="10:15" />', $element->toHtml());
    }
}
