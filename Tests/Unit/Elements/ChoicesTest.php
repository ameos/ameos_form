<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Checkbox;
use Ameos\AmeosForm\Elements\Radio;
use Ameos\AmeosForm\Elements\Rating;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;

final class ChoicesTest extends AbstractUnitTestCase
{
    #[Test]
    public function checkboxRendersCheckedItems(): void
    {
        $element = new Checkbox('tx_test[opt]', 'opt', ['items' => ['a' => 'A', 'b' => '<i>B</i>'], 'separator' => '<br />'], $this->createFormStub());
        $element->setValue(['b']);

        self::assertSame(
            '<input id="tx_test_opt-a" name="tx_test[opt][]" type="checkbox" value="a" /><label for="tx_test_opt-a">A</label><br />'
            . '<input id="tx_test_opt-b" name="tx_test[opt][]" type="checkbox" value="b" checked="checked" /><label for="tx_test_opt-b"><i>B</i></label><br />',
            $element->toHtml(),
        );
        self::assertSame(['b'], $element->getValue());
    }

    #[Test]
    public function checkboxValueIsStoredAsCommaSeparatedList(): void
    {
        $element = new Checkbox('tx_test[opt]', 'opt', ['items' => ['a' => 'A', 'b' => 'B']], $this->createFormStub());
        $element->setValue(['a', 'b']);

        self::assertSame(['a', 'b'], $element->getValue());
        self::assertSame(
            [
                'elementname' => 'opt',
                'elementvalue' => ['a', 'b'],
                'field' => 'opt',
                'type' => 'logicalOr',
                'clauses' => [
                    ['field' => 'opt', 'type' => 'contains', 'value' => 'a'],
                    ['field' => 'opt', 'type' => 'contains', 'value' => 'b'],
                ],
            ],
            $element->getClause(),
        );
    }

    #[Test]
    public function emptyCheckboxHasNoValueAndNoClause(): void
    {
        $element = new Checkbox('tx_test[opt]', 'opt', ['items' => ['a' => 'A']], $this->createFormStub());

        self::assertSame([], $element->getValue());
        self::assertFalse($element->getClause());
    }

    #[Test]
    public function checkboxRenderingInformationContainsChoices(): void
    {
        $element = new Checkbox('tx_test[opt]', 'opt', ['items' => ['a' => 'A']], $this->createFormStub());
        $data = $element->getRenderingInformation();

        self::assertSame(
            ['a' => [
                'input' => '<input id="tx_test_opt-a" name="tx_test[opt][]" type="checkbox" value="a" />',
                'label' => '<label for="tx_test_opt-a">A</label>',
            ]],
            $data['choices'],
        );
    }

    #[Test]
    public function radioRendersCheckedItemWithAttributes(): void
    {
        $element = new Radio('tx_test[r]', 'r', ['items' => [1 => 'One', 2 => 'Two'], 'class' => 'radio'], $this->createFormStub());
        $element->setValue('2');

        self::assertSame(
            '<input id="tx_test_r-1" name="tx_test[r]" type="radio" value="1" class="radio" /><label for="tx_test_r-1">One</label>'
            . '<input id="tx_test_r-2" name="tx_test[r]" type="radio" value="2" checked="checked" class="radio" /><label for="tx_test_r-2">Two</label>',
            $element->toHtml(),
        );
        self::assertSame('equals', $element->getClause()['type'] ?? null);
    }

    #[Test]
    public function radioWithoutItemsThrowsException(): void
    {
        $this->expectException(BadConstraintException::class);
        (new Radio('tx_test[r]', 'r', [], $this->createFormStub()))->toHtml();
    }

    #[Test]
    public function ratingBuildsItemsFromBounds(): void
    {
        $element = new Rating('tx_test[rate]', 'rate', ['min' => 1, 'max' => 3], $this->createFormStub());

        self::assertSame([3 => '★', 2 => '★', 1 => '★'], $element->getConfigurationItem('items'));
        self::assertStringStartsWith('<span class="rating"><input id="tx_test_rate-3"', $element->toHtml());
    }
}
