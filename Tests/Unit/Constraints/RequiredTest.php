<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Validators;

use Ameos\AmeosForm\Constraints\Required;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class RequiredTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function requiredIsValid()
    {
        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);

        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', [], $form);
        $constraint = new Required('field mandatory', [], $element, $form);

        $element->addConstraint($constraint);
        $element->setValue('test');

        $result = $constraint->isValid('test');

        $this->assertTrue($result);
    }

    #[Test]
    public function requiredIsNotValid()
    {
        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);

        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', [], $form);
        $constraint = new Required('field mandatory', [], $element, $form);

        $element->addConstraint($constraint);
        $element->setValue('');

        $result = $constraint->isValid('');

        $this->assertFalse($result);
    }
}
