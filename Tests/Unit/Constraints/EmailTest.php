<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Validators;

use Ameos\AmeosForm\Constraints\Email;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class EmailTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function emailIsValid()
    {
        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);

        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', [], $form);
        $constraint = new Email('email not valid', [], $element, $form);

        $element->addConstraint($constraint);
        $element->setValue('john.doe@example.org');

        $result = $constraint->isValid('john.doe@example.org');

        $this->assertTrue($result);
    }

    #[Test]
    public function emailIsNotValid()
    {
        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);

        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', [], $form);
        $constraint = new Email('email not valid', [], $element, $form);

        $element->addConstraint($constraint);
        $element->setValue('john.doeexample.org');

        $result = $constraint->isValid('john.doeexample.org');

        $this->assertFalse($result);
    }
}
