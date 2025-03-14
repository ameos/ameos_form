<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Validators;

use Ameos\AmeosForm\Constraints\Sameas;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class SameasTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function sameasIsValid()
    {
        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $element1 = new Text('tx_ameosform-unittest[input-text-1]', 'input-text-1', [], $form);
        $element2 = new Text('tx_ameosform-unittest[input-text-2]', 'input-text-2', [], $form);

        $form->method('getErrorManager')->willReturn($errorManager);
        $form->method('getElement')->willReturn($element2);

        $constraint = new Sameas('must be the same', ['sameas' => 'input-text-1'], $element2, $form);
        $element2->addConstraint($constraint);
        $element1->setValue('test');
        $element2->setValue('test');
        $result = $constraint->isValid('test');

        $this->assertTrue($result);
    }

    #[Test]
    public function sameasIsNotValid()
    {
        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $element1 = new Text('tx_ameosform-unittest[input-text-1]', 'input-text-1', [], $form);
        $element2 = new Text('tx_ameosform-unittest[input-text-2]', 'input-text-2', [], $form);

        $form->method('getErrorManager')->willReturn($errorManager);
        $form->method('getElement')->willReturn($element2);

        $constraint = new Sameas('must be the same', ['sameas' => 'input-text-1'], $element2, $form);
        $element2->addConstraint($constraint);
        $element1->setValue('test');
        $element2->setValue('test2');
        $result = $constraint->isValid('test');

        $this->assertFalse($result);
    }
}
