<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Dropdown;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

final class DropdownTest extends AbstractUnitTestCase
{
    private const array ITEMS = ['value-1' => 'Value 1', 'value-2' => 'Value 2', 'value-3' => 'Value 3'];

    private const string EXPECTED_HTML = '<select id="tx_ameosform-unittest_my-select" name="tx_ameosform-unittest[my-select]" '
        . 'style="display: block;" '
        . 'title="My text field" '
        . 'class="my-css-class" '
        . 'customattr="custom-value">'
        . '<option value="">Here your value</option>'
        . '<option value="value-1">Value 1</option>'
        . '<option value="value-2" selected="selected">Value 2</option>'
        . '<option value="value-3">Value 3</option>'
        . '</select>';

    /**
     * @return array<string, mixed>
     */
    private function getConfiguration(): array
    {
        return [
            'defaultValue' => 'value-2',
            'class' => 'my-css-class',
            'title' => 'My text field',
            'custom' => 'customattr="custom-value"',
            'placeholder' => 'Here your value',
            'items' => self::ITEMS,
        ];
    }

    #[Test]
    public function elementRenderer(): void
    {
        $element = new Dropdown('tx_ameosform-unittest[my-select]', 'my-select', $this->getConfiguration(), $this->createFormStub());
        $element->with('style', 'display: block;');

        self::assertSame(self::EXPECTED_HTML, $element->toHtml());
    }

    #[Test]
    public function elementRenderingInformation(): void
    {
        $configuration = $this->getConfiguration() + ['otherdata' => 'test'];
        $element = new Dropdown('tx_ameosform-unittest[my-select]', 'my-select', $configuration, $this->createFormStub());
        $element->with('style', 'display: block;');

        self::assertEquals(
            $configuration + [
                'style' => 'display: block;',
                'optionValueField' => 'uid',
                '__compiled' => self::EXPECTED_HTML,
                'name' => 'my-select',
                'value' => 'value-2',
                'absolutename' => 'tx_ameosform-unittest[my-select]',
                'htmlid' => 'tx_ameosform-unittest_my-select',
                'errors' => [],
                'isvalid' => true,
                'hasError' => false,
                'required' => false,
                'choices' => self::ITEMS,
            ],
            $element->getRenderingInformation(),
        );
    }

    #[Test]
    public function multipleDropdownSelectsAllValuesAndSearchesWithIn(): void
    {
        $element = new Dropdown('tx_test[tags]', 'tags', ['items' => self::ITEMS, 'multiple' => true], $this->createFormStub());
        $element->setValue(['value-1', 'value-3']);
        $html = $element->toHtml();

        self::assertStringStartsWith('<select id="tx_test_tags" name="tx_test[tags][]" multiple>', $html);
        self::assertStringContainsString('<option value="value-1" selected="selected">', $html);
        self::assertStringContainsString('<option value="value-2">', $html);
        self::assertStringContainsString('<option value="value-3" selected="selected">', $html);
        self::assertSame('in', $element->getClause()['type'] ?? null);
    }

    #[Test]
    public function modelsAndObjectStorageValuesAreSupported(): void
    {
        $models = [$this->createModel(1, 'First'), $this->createModel(2, 'Second <b>'), $this->createModel(3, 'Third')];
        $selected = new ObjectStorage();
        $selected->attach($models[0]);
        $selected->attach($models[2]);

        $element = new Dropdown('tx_test[rel]', 'rel', ['items' => new \ArrayIterator($models), 'optionLabelField' => 'title'], $this->createFormStub());
        $element->setValue($selected);

        self::assertSame(
            '<select id="tx_test_rel" name="tx_test[rel]">'
            . '<option value="1" selected="selected">First</option>'
            . '<option value="2">Second &lt;b&gt;</option>'
            . '<option value="3" selected="selected">Third</option></select>',
            $element->toHtml(),
        );
    }

    #[Test]
    public function missingItemsThrowsException(): void
    {
        $this->expectException(BadConstraintException::class);
        (new Dropdown('tx_test[rel]', 'rel', [], $this->createFormStub()))->toHtml();
    }

    private function createModel(int $uid, string $title): AbstractEntity
    {
        $model = new class extends AbstractEntity {
            protected string $title = '';

            public function getTitle(): string
            {
                return $this->title;
            }

            public function setTitle(string $title): void
            {
                $this->title = $title;
            }
        };
        $model->_setProperty('uid', $uid);
        $model->setTitle($title);
        return $model;
    }
}
