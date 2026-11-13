<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements;
use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class SimpleElementsTest extends AbstractUnitTestCase
{
    /**
     * @return iterable<string, array{class-string<ElementInterface>, array<string, mixed>, mixed, string}>
     */
    public static function elementsDataProvider(): iterable
    {
        yield 'email' => [Elements\Email::class, [], 'a@b.c', '<input type="email" id="tx_test_field" name="tx_test[field]" value="a@b.c" />'];
        yield 'hidden' => [Elements\Hidden::class, [], '12', '<input type="hidden" id="tx_test_field" name="tx_test[field]" value="12" />'];
        yield 'color' => [Elements\Color::class, [], '#ff0000', '<input type="color" id="tx_test_field" name="tx_test[field]" value="#ff0000" />'];
        yield 'url' => [Elements\Url::class, [], 'https://typo3.org', '<input type="url" id="tx_test_field" name="tx_test[field]" value="https://typo3.org" />'];
        yield 'tel' => [Elements\Tel::class, ['pattern' => '[0-9]{10}'], '0102030405', '<input type="tel" id="tx_test_field" name="tx_test[field]" value="0102030405" pattern="[0-9]{10}" />'];
        yield 'textarea' => [Elements\Textarea::class, [], 'a<b', '<textarea id="tx_test_field" name="tx_test[field]">a&lt;b</textarea>'];
        yield 'number' => [Elements\Number::class, ['min' => 1, 'max' => 10, 'step' => '0.5'], '2', '<input type="number" id="tx_test_field" name="tx_test[field]" value="2" min="1" max="10" step="0.5" />'];
        yield 'range' => [Elements\Range::class, ['min' => 0, 'max' => 5], '3', '<input type="range" id="tx_test_field" name="tx_test[field]" value="3" min="0" max="5" />'];
        yield 'checksingle checked' => [Elements\Checksingle::class, [], '1', '<input type="checkbox" id="tx_test_field" name="tx_test[field]" value="1" checked="checked" />'];
        yield 'checksingle unchecked' => [Elements\Checksingle::class, [], '', '<input type="checkbox" id="tx_test_field" name="tx_test[field]" value="1" />'];
        yield 'submit' => [Elements\Submit::class, ['label' => 'Send "now"'], null, '<input type="submit" id="tx_test_field" value="Send &quot;now&quot;" name="tx_test[field]" />'];
        yield 'submit image' => [Elements\Submit::class, ['src' => '/btn.png'], null, '<input type="image" src="/btn.png" id="tx_test_field" value="Envoyer" name="tx_test[field]" />'];
        yield 'button' => [Elements\Button::class, ['label' => '<b>Go</b>', 'type' => 'submit'], null, '<button id="tx_test_field" name="tx_test[field]" type="submit">&lt;b&gt;Go&lt;/b&gt;</button>'];
        yield 'button with html label' => [Elements\Button::class, ['label' => '<b>Go</b>', 'htmllabel' => true], null, '<button id="tx_test_field" name="tx_test[field]"><b>Go</b></button>'];
        yield 'password not filled' => [Elements\Password::class, [], 'secret', '<input type="password" id="tx_test_field" name="tx_test[field]" />'];
        yield 'password filled' => [Elements\Password::class, ['fill_value' => true], 'secret', '<input type="password" id="tx_test_field" name="tx_test[field]" value="secret" />'];
    }

    /**
     * @param class-string<ElementInterface> $className
     * @param array<string, mixed> $configuration
     */
    #[Test]
    #[DataProvider('elementsDataProvider')]
    public function elementIsRendered(string $className, array $configuration, mixed $value, string $expected): void
    {
        $element = new $className('tx_test[field]', 'field', $configuration, $this->createFormStub());
        if ($value !== null) {
            $element->setValue($value);
        }

        self::assertSame($expected, $element->toHtml());
    }

    #[Test]
    public function numberRenderingInformationContainsBounds(): void
    {
        $element = new Elements\Number('tx_test[field]', 'field', ['min' => 1, 'max' => 10, 'step' => 2], $this->createFormStub());
        $data = $element->getRenderingInformation();

        self::assertSame([1, 10, 2], [$data['min'], $data['max'], $data['step']]);
    }

    #[Test]
    public function passwordIsRefilledOnlyWhenFormHasErrors(): void
    {
        $form = $this->createForm(['password' => 'secret', 'issubmitted' => '1']);
        $form->add('password', 'password');
        $form->addConstraint('password', 'custom', 'invalid', ['method' => static fn(): bool => false]);

        self::assertFalse($form->isValid());
        self::assertStringContainsString('value="secret"', $this->element($form, 'password')->toHtml());
    }

    #[Test]
    public function submitAndButtonDetectClick(): void
    {
        $form = $this->createForm(['issubmitted' => '1', 'save' => '']);
        $form->add('save', 'submit')->add('cancel', 'submit')->add('reset', 'button', ['type' => 'button']);

        $save = $form->getElement('save');
        self::assertInstanceOf(Elements\Submit::class, $save);
        self::assertTrue($save->isClicked());
        self::assertSame($save, $form->getSubmitter());
        self::assertCount(2, $form->getSubmitterElements());
        self::assertFalse($save->isSearchable());
    }
}
