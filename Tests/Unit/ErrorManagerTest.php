<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit;

use Ameos\AmeosForm\Elements\Email;
use Ameos\AmeosForm\Elements\Text;
use PHPUnit\Framework\Attributes\Test;

final class ErrorManagerTest extends AbstractUnitTestCase
{
    #[Test]
    public function addError(): void
    {
        $form = $this->createFormStub();
        $textElement = new Text('text_element', 'text_element', [], $form);
        $mailElement = new Email('mail_element', 'mail_element', [], $form);
        $errorManager = $form->getErrorManager();

        $errorManager->add('Error', 'text_element');

        self::assertFalse($errorManager->elementIsValid($textElement));
        self::assertTrue($errorManager->elementIsValid($mailElement));
        self::assertSame(['text_element' => ['Error']], $errorManager->getErrors());
        self::assertSame(['Error'], $errorManager->getErrorsFor($textElement));
        self::assertSame([], $errorManager->getErrorsFor($mailElement));
        self::assertSame(['Error'], $errorManager->getFlatErrors());
        self::assertFalse($errorManager->isValid());
    }

    #[Test]
    public function severalErrorsOfTheSameElementAreKept(): void
    {
        $errorManager = $this->createFormStub()->getErrorManager();
        $errorManager->add('First', 'field')->add('Second', 'field')->add('Other', 'other');

        self::assertSame(['field' => ['First', 'Second'], 'other' => ['Other']], $errorManager->getErrors());
        self::assertSame(['First', 'Second', 'Other'], $errorManager->getFlatErrors());
    }

    #[Test]
    public function elementConstraintsAreCheckedOnlyOnceWhenSubmitted(): void
    {
        $calls = 0;
        $form = $this->createForm(['field' => '', 'issubmitted' => '1']);
        $form->add('field', 'text');
        $form->addConstraint('field', 'custom', 'Invalid', ['method' => static function () use (&$calls): bool {
            $calls++;
            return false;
        }]);

        self::assertFalse($form->getErrorManager()->isValid());
        self::assertSame(['Invalid'], $form->getErrors());
        self::assertSame(['field' => ['Invalid']], $form->getErrorManager()->getErrors());
        self::assertSame(1, $calls);
    }

    #[Test]
    public function constraintsAreNotCheckedWhenNotSubmitted(): void
    {
        $form = $this->createForm();
        $form->add('field', 'text')->addConstraint('field', 'required', 'Required');

        self::assertTrue($form->getErrorManager()->isValid());
    }
}
