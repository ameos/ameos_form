<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Form;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\Elements\Upload;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use Ameos\AmeosForm\Event\ValidFormEvent;
use Ameos\AmeosForm\Exception\RepositoryNotFoundException;
use Ameos\AmeosForm\Service\FormService;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

final class FormTest extends AbstractUnitTestCase
{
    #[Test]
    public function submittedValuesAreBound(): void
    {
        $form = $this->createForm(['issubmitted' => '1', 'name' => 'John', 'opt' => ['a', 'b']]);
        $form->add('name', 'text')->add('opt', 'checkbox', ['items' => ['a' => 'A', 'b' => 'B']])->add('other', 'text');

        self::assertTrue($form->isSubmitted());
        self::assertSame('John', $this->element($form, 'name')->getValue());
        self::assertSame(['a', 'b'], $this->element($form, 'opt')->getValue());
        self::assertSame('', $this->element($form, 'other')->getValue());
        self::assertTrue($form->has('name'));
        self::assertFalse($form->getElement('unknown'));
        self::assertInstanceOf(Text::class, $form->getElement('name'));
        self::assertSame('tx_test[name]', $this->element($form, 'name')->getAbsoluteName());
    }

    #[Test]
    public function notSubmittedForm(): void
    {
        $form = $this->createForm();
        $form->add('name', 'text', ['defaultValue' => 'default']);

        self::assertFalse($form->isSubmitted());
        self::assertFalse($form->getSubmitter());
        self::assertSame('default', $this->element($form, 'name')->getValue());
    }

    #[Test]
    public function bindEventCanChangeValue(): void
    {
        $eventDispatcher = $this->createStub(EventDispatcherInterface::class);
        $eventDispatcher->method('dispatch')->willReturnCallback(static function (object $event): object {
            if ($event instanceof BindValueFromRequestEvent) {
                $value = $event->getValue();
                $event->setValue(is_string($value) ? strtoupper($value) : $value);
            }
            return $event;
        });
        $form = $this->createForm(['name' => 'john'], [], $eventDispatcher);
        $form->add('name', 'text');

        self::assertSame('JOHN', $this->element($form, 'name')->getValue());
    }

    #[Test]
    public function validFormEventIsDispatchedOnce(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::once())->method('dispatch')->with(self::isInstanceOf(ValidFormEvent::class))->willReturnArgument(0);

        $form = $this->createForm(['issubmitted' => '1'], [], $eventDispatcher);
        $form->add('name', 'text', ['defaultValue' => 'John'])->addConstraint('name', 'required', 'Name is required');

        self::assertTrue($form->isValid());
        self::assertTrue($form->isValid());
    }

    #[Test]
    public function validFormEventIsNotDispatchedWhenNotSubmitted(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::never())->method('dispatch');

        $form = $this->createForm([], [], $eventDispatcher);
        $form->add('password', 'password');

        self::assertTrue($form->isValid());
    }

    #[Test]
    public function invalidFormDoesNotDispatchEvent(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::never())->method('dispatch');

        $form = $this->createForm(['issubmitted' => '1'], [], $eventDispatcher);
        $form->add('name', 'text')
            ->add('email', 'email')
            ->addConstraint('name', 'required', 'Name is required')
            ->validator('email', 'email', 'Email is invalid')
            ->addConstraint('unknown', 'required', 'ignored');
        $this->element($form, 'email')->setValue('invalid');

        self::assertFalse($form->isValid());
        self::assertSame(['Name is required', 'Email is invalid'], $form->getErrors());
        self::assertSame(['Name is required'], $this->element($form, 'name')->getErrors());
        self::assertTrue($this->element($form, 'name')->getRenderingInformation()['required']);
    }

    #[Test]
    public function entityIsReadAndUpdated(): void
    {
        $entity = new class extends AbstractEntity {
            protected string $firstName = 'Jane';

            public function getFirstName(): string
            {
                return $this->firstName;
            }

            public function setFirstName(string $firstName): void
            {
                $this->firstName = $firstName;
            }
        };

        $form = $this->createForm();
        $form->attachEntity($entity)->add('first_name', 'text');
        self::assertSame('Jane', $this->element($form, 'first_name')->getValue());
        self::assertSame($entity, $form->getAttachedEntity());

        $form = $this->createForm(['first_name' => 'John']);
        $form->attachEntity($entity)->add('first_name', 'text');
        self::assertSame('John', $entity->getFirstName());
    }

    #[Test]
    public function uploadValueMergesUploadedAndTemporaryFiles(): void
    {
        $values = [];
        $eventDispatcher = $this->createStub(EventDispatcherInterface::class);
        $eventDispatcher->method('dispatch')->willReturnCallback(static function (object $event) use (&$values): object {
            if ($event instanceof BindValueFromRequestEvent) {
                $values[] = $event->getValue();
            }
            return $event;
        });

        $form = $this->createForm(['cv' => ['temporary' => ['cv.pdf']]], [], $eventDispatcher);
        $form->add('cv', 'upload', ['directory' => 'fileadmin']);

        self::assertInstanceOf(Upload::class, $form->get('cv'));
        self::assertSame([['upload' => [], 'temporary' => ['cv.pdf']]], $values);
    }

    #[Test]
    public function searchResultsUseElementClausesAndAreStoredInSession(): void
    {
        $frontendUser = $this->createMock(FrontendUserAuthentication::class);
        $frontendUser->expects(self::once())->method('setAndSaveSessionData')->with('form-tx_test-clauses', self::callback(
            static fn(array $clauses): bool => array_keys($clauses) === ['title'],
        ));

        $repository = $this->createMock(SearchableRepositoryInterface::class);
        $repository->expects(self::once())->method('findByClausesArray')->with(
            [
                ['elementname' => 'title', 'elementvalue' => 'foo', 'field' => 'title', 'type' => 'like', 'value' => '%foo%'],
                ['type' => 'equals', 'field' => 'hidden', 'value' => 0],
            ],
            'title',
            'DESC',
        )->willReturn(['result']);

        $request = (new ServerRequest('https://example.com/', 'POST'))
            ->withParsedBody(['tx_test' => ['title' => 'foo', 'submit' => '', 'issubmitted' => '1']])
            ->withAttribute('frontend.user', $frontendUser);
        $form = (new FormService($this->createStub(EventDispatcherInterface::class)))->create('tx_test', $repository, $request);
        $form->add('title', 'text')->add('empty', 'text')->add('submit', 'submit')->addWhereClause(['type' => 'equals', 'field' => 'hidden', 'value' => 0]);

        self::assertSame(['result'], $form->getResults('title', 'DESC'));
    }

    #[Test]
    public function searchValueIsRestoredFromSession(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->method('getSessionData')->willReturn(['title' => ['elementvalue' => 'stored']]);
        $request = (new ServerRequest('https://example.com/'))->withAttribute('frontend.user', $frontendUser);

        $form = (new FormService($this->createStub(EventDispatcherInterface::class)))
            ->create('tx_test', $this->createStub(SearchableRepositoryInterface::class), $request);
        $form->add('title', 'text');
        self::assertSame('stored', $this->element($form, 'title')->getValue());

        $form = (new FormService($this->createStub(EventDispatcherInterface::class)))
            ->create('tx_test', $this->createStub(SearchableRepositoryInterface::class), $request);
        $form->disableSearchStoringInSession()->add('title', 'text');
        self::assertSame('', $this->element($form, 'title')->getValue());
    }

    #[Test]
    public function resultsWithoutRepositoryThrowException(): void
    {
        $this->expectException(RepositoryNotFoundException::class);
        $this->createForm()->getResults();
    }

    #[Test]
    public function optionsCanBeToggled(): void
    {
        $form = $this->createForm();
        self::assertTrue($form->csrftokenIsEnabled());
        self::assertTrue($form->honeypotIsEnabled());
        self::assertTrue($form->searchIsStoredInSession());

        $form->disableCsrftoken()->disableHoneypot()->disableSearchStoringInSession();
        self::assertFalse($form->csrftokenIsEnabled());
        self::assertFalse($form->honeypotIsEnabled());
        self::assertFalse($form->searchIsStoredInSession());

        $form->enableCsrftoken()->enableHoneypot()->storeSearchInSession();
        self::assertTrue($form->csrftokenIsEnabled());
        self::assertTrue($form->honeypotIsEnabled());
        self::assertTrue($form->searchIsStoredInSession());
    }

    #[Test]
    public function formRendersAllElements(): void
    {
        $form = $this->createForm()->add('a', 'hidden', ['defaultValue' => '1'])->add('b', 'hidden');

        self::assertSame(
            '<input type="hidden" id="tx_test_a" name="tx_test[a]" value="1" /><input type="hidden" id="tx_test_b" name="tx_test[b]" value="" />',
            $form->toHtml(),
        );
    }
}
