<?php

namespace Ameos\AmeosForm\Tests\Unit;

use Ameos\AmeosForm\Elements\Email;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ErrorManagerTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function testAddError()
    {
        $form = $this->createMock(Form::class);
        $textElement = new Text('text_element', 'text_element', [], $form);
        $mailElement = new Email('mail_element', 'mail_element', [], $form);

        $errorManager = new ErrorManager($form);
        $errorManager->add('Error', 'text_element');

        $this->assertEquals(false, $errorManager->elementIsValid($textElement));
        $this->assertEquals(true, $errorManager->elementIsValid($mailElement));
        $this->assertEquals(['text_element' => ['Error']], $errorManager->getErrors());
        $this->assertEquals(['Error'], $errorManager->getErrorsFor($textElement));
        $this->assertEquals([], $errorManager->getErrorsFor($mailElement));
        $this->assertEquals(['Error'], $errorManager->getFlatErrors());
    }
}
