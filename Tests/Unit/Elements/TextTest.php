<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;

final class TextTest extends AbstractUnitTestCase
{
    private const string EXPECTED_HTML = '<input type="text" id="tx_ameosform-unittest_input-text" '
        . 'name="tx_ameosform-unittest[input-text]" '
        . 'value="my-value" '
        . 'placeholder="Here your value" '
        . 'style="display: block;" '
        . 'title="My text field" '
        . 'class="my-css-class" '
        . 'customattr="custom-value" />';

    /**
     * @return array<string, mixed>
     */
    private function getConfiguration(): array
    {
        return [
            'defaultValue' => 'my-value',
            'class' => 'my-css-class',
            'title' => 'My text field',
            'custom' => 'customattr="custom-value"',
            'placeholder' => 'Here your value',
        ];
    }

    #[Test]
    public function elementRenderer(): void
    {
        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', $this->getConfiguration(), $this->createFormStub());
        $element->with('style', 'display: block;');

        self::assertSame(self::EXPECTED_HTML, $element->toHtml());
        self::assertSame(self::EXPECTED_HTML, (string) $element);
    }

    #[Test]
    public function elementRenderingInformation(): void
    {
        $configuration = $this->getConfiguration() + ['otherdata' => 'test'];
        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', $configuration, $this->createFormStub());
        $element->with('style', 'display: block;');

        self::assertEquals(
            $configuration + [
                'style' => 'display: block;',
                '__compiled' => self::EXPECTED_HTML,
                'name' => 'input-text',
                'value' => 'my-value',
                'absolutename' => 'tx_ameosform-unittest[input-text]',
                'htmlid' => 'tx_ameosform-unittest_input-text',
                'errors' => [],
                'isvalid' => true,
                'hasError' => false,
                'required' => false,
            ],
            $element->getRenderingInformation(),
        );
    }

    #[Test]
    public function submittedValueIsEscaped(): void
    {
        $element = new Text('tx_test[name]', 'name', [], $this->createFormStub());
        $element->setValue('"><script>alert(1)</script>');

        self::assertStringContainsString('value="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"', $element->toHtml());
        self::assertStringNotContainsString('<script>', $element->toHtml());
    }

    #[Test]
    public function configurationAttributesAreEscapedWithoutDoubleEncoding(): void
    {
        $element = new Text('tx_test[name]', 'name', ['placeholder' => 'Tom & "Jerry"', 'title' => 'A&amp;B'], $this->createFormStub());

        self::assertStringContainsString('placeholder="Tom &amp; &quot;Jerry&quot;"', $element->toHtml());
        self::assertStringContainsString('title="A&amp;B"', $element->toHtml());
    }

    #[Test]
    public function errorClassIsAddedWhenElementHasErrors(): void
    {
        $form = $this->createFormStub(true);
        $element = new Text('tx_test[name]', 'name', ['class' => 'field', 'errorclass' => 'is-invalid'], $form);
        self::assertStringContainsString('class="field"', $element->toHtml());

        $form->getErrorManager()->add('error', 'name');
        self::assertStringContainsString('class="field is-invalid"', $element->toHtml());
    }

    #[Test]
    public function datalistIsRendered(): void
    {
        $element = new Text('tx_test[city]', 'city', ['datalist' => ['paris' => 'Paris', 'nice' => 'Nice']], $this->createFormStub());

        self::assertSame(
            '<input type="text" id="tx_test_city" name="tx_test[city]" value="" list="tx_test_city-datalist" />'
            . '<datalist id="tx_test_city-datalist"><option value="paris" label="Paris">Paris</option>'
            . '<option value="nice" label="Nice">Nice</option></datalist>',
            $element->toHtml(),
        );
    }

    #[Test]
    public function defaultClauseIsLikeSearch(): void
    {
        $element = new Text('tx_test[title]', 'title', ['searchfield' => 'header'], $this->createFormStub());
        self::assertFalse($element->getClause());

        $element->setValue('foo');
        self::assertSame(
            ['elementname' => 'title', 'elementvalue' => 'foo', 'field' => 'header', 'type' => 'like', 'value' => '%foo%'],
            $element->getClause(),
        );
    }

    #[Test]
    public function overrideClauseIsUsed(): void
    {
        $element = new Text('tx_test[title]', 'title', [], $this->createFormStub());
        $element->setOverrideClause(static fn(mixed $value): array => ['type' => 'equals', 'field' => 'x', 'value' => $value]);
        $element->setValue('foo');

        self::assertSame(
            ['type' => 'equals', 'field' => 'x', 'value' => 'foo', 'elementname' => 'title', 'elementvalue' => 'foo'],
            $element->getClause(),
        );
    }
}
