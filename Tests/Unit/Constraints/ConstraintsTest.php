<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Constraints;

use Ameos\AmeosForm\Constraints\Custom;
use Ameos\AmeosForm\Constraints\Email;
use Ameos\AmeosForm\Constraints\Fileextension;
use Ameos\AmeosForm\Constraints\Required;
use Ameos\AmeosForm\Constraints\Sameas;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\Elements\Textdate;
use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ConstraintsTest extends AbstractUnitTestCase
{
    #[Test]
    public function customIsValid(): void
    {
        $form = $this->createFormStub();
        $element = new Text('tx_test[f]', 'f', [], $form);
        $constraint = new Custom('custom error', ['method' => static fn(mixed $value, Form $form): bool => $value === 'test'], $element, $form);

        self::assertTrue($constraint->isValid('test'));
        self::assertFalse($constraint->isValid('other'));
        self::assertSame('custom error', $constraint->getMessage());
    }

    #[Test]
    public function customWithoutCallableThrowsException(): void
    {
        $form = $this->createFormStub();
        $this->expectException(BadConfigurationException::class);
        (new Custom('error', [], new Text('tx_test[f]', 'f', [], $form), $form))->isValid('x');
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function emailDataProvider(): iterable
    {
        yield 'valid' => ['john.doe@example.com', true];
        yield 'empty is valid (use required)' => ['', true];
        yield 'null is valid' => [null, true];
        yield 'invalid' => ['john.doe', false];
        yield 'array' => [['a@b.c'], false];
    }

    #[Test]
    #[DataProvider('emailDataProvider')]
    public function emailIsValidated(mixed $value, bool $expected): void
    {
        $form = $this->createFormStub();
        self::assertSame($expected, (new Email('error', [], new Text('tx_test[f]', 'f', [], $form), $form))->isValid($value));
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function requiredDataProvider(): iterable
    {
        yield 'string' => ['value', true];
        yield 'zero' => ['0', true];
        yield 'empty string' => ['', false];
        yield 'whitespace' => ['   ', false];
        yield 'null' => [null, false];
        yield 'array' => [['a'], true];
        yield 'empty array' => [[], false];
        yield 'object' => [new \stdClass(), true];
    }

    #[Test]
    #[DataProvider('requiredDataProvider')]
    public function requiredIsValidated(mixed $value, bool $expected): void
    {
        $form = $this->createFormStub();
        self::assertSame($expected, (new Required('error', [], new Text('tx_test[f]', 'f', [], $form), $form))->isValid($value));
    }

    #[Test]
    public function requiredTextdateNeedsADate(): void
    {
        $form = $this->createFormStub();
        $constraint = new Required('error', [], new Textdate('tx_test[d]', 'd', [], $form), $form);

        self::assertTrue($constraint->isValid(new \DateTime()));
        self::assertFalse($constraint->isValid('2024-01-01'));
    }

    #[Test]
    public function sameasIsValid(): void
    {
        $form = $this->createStub(Form::class);
        $element1 = new Text('tx_test[f1]', 'f1', [], $form);
        $element2 = new Text('tx_test[f2]', 'f2', [], $form);
        $form->method('getElement')->willReturnMap([['f1', $element1], ['f2', $element2]]);
        $constraint = new Sameas('must be the same', ['sameas' => 'f1'], $element2, $form);

        $element1->setValue('test');
        self::assertTrue($constraint->isValid('test'));
        self::assertFalse($constraint->isValid('test2'));
    }

    #[Test]
    public function sameasWithUnknownElementIsNotValid(): void
    {
        $form = $this->createStub(Form::class);
        $form->method('getElement')->willReturn(false);
        $constraint = new Sameas('error', ['sameas' => 'unknown'], new Text('tx_test[f]', 'f', [], $form), $form);

        self::assertFalse($constraint->isValid('test'));
    }

    #[Test]
    public function fileextensionRejectsAndRemovesInvalidFiles(): void
    {
        $form = $this->createFormStub();
        $element = new Upload('tx_test[file]', 'file', ['directory' => 'fileadmin'], $form);
        $element->setValue(['cv.PDF', 'virus.exe']);
        $constraint = new Fileextension('%file_name%: only %extensions%', ['allowed' => 'pdf,doc'], $element, $form);

        self::assertFalse($constraint->isValid($element->getValue()));
        self::assertSame(['cv.PDF'], $element->getFileNames());
        self::assertSame(['virus.exe: only pdf, doc'], $form->getErrorManager()->getErrorsFor($element));
        self::assertNull($constraint->getMessage());
        self::assertTrue($constraint->isValid([]));
    }

    #[Test]
    public function fileextensionRequiresUploadElement(): void
    {
        $form = $this->createFormStub();
        $constraint = new Fileextension('error', ['allowed' => 'pdf'], new Text('tx_test[f]', 'f', [], $form), $form);

        $this->expectException(BadConfigurationException::class);
        $constraint->isValid(['virus.exe']);
    }
}
