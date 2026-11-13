<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Form;

use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * CSRF token and honeypot are checked when the form is submitted
 */
final class FormProtectionTest extends AbstractUnitTestCase
{
    /** @var array<string, mixed> */
    private array $session = [];

    #[Test]
    public function csrfTokenIsCreatedOnceAndStoredInSession(): void
    {
        $form = $this->createFrontendForm([]);

        $token = $form->getCsrfToken();

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
        self::assertSame($token, $this->session['tx_test-csrftoken']);
        self::assertSame($token, $form->getCsrfToken());
        self::assertSame($token, $this->createFrontendForm([])->getCsrfToken(), 'token is reused (e.g. several tabs)');
    }

    #[Test]
    public function tokenIsNotokenWithoutFrontendUser(): void
    {
        self::assertSame('notoken', $this->createForm()->getCsrfToken());
    }

    #[Test]
    public function submittedFormWithValidTokenIsValid(): void
    {
        $this->session['tx_test-csrftoken'] = 'secret';
        $form = $this->createFrontendForm(['issubmitted' => '1', 'csrftoken' => 'secret', 'winnie' => '']);

        self::assertTrue($form->isValid());
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function invalidTokenDataProvider(): iterable
    {
        yield 'missing token' => [['issubmitted' => '1']];
        yield 'wrong token' => [['issubmitted' => '1', 'csrftoken' => 'forged']];
        yield 'token as array' => [['issubmitted' => '1', 'csrftoken' => ['secret']]];
    }

    /**
     * @param array<string, mixed> $body
     */
    #[Test]
    #[DataProvider('invalidTokenDataProvider')]
    public function submittedFormWithInvalidTokenIsNotValid(array $body): void
    {
        $this->stubTranslations();
        $this->session['tx_test-csrftoken'] = 'secret';
        $form = $this->createFrontendForm($body);
        $form->add('name', 'text');

        self::assertFalse($form->isValid());
        self::assertSame(['error.csrf'], $form->getErrors());
        self::assertSame(['error.csrf'], $form->getErrorManager()->getErrors()[Form::FORM_ERROR_KEY]);
        self::assertTrue($this->element($form, 'name')->isValid());
    }

    #[Test]
    public function tokenIsInvalidWhenSessionHasNoToken(): void
    {
        $this->stubTranslations();
        $form = $this->createFrontendForm(['issubmitted' => '1', 'csrftoken' => '']);

        self::assertFalse($form->isValid());
    }

    #[Test]
    public function csrfTokenCheckCanBeDisabled(): void
    {
        $form = $this->createFrontendForm(['issubmitted' => '1', 'csrftoken' => 'forged']);
        $form->disableCsrftoken();

        self::assertTrue($form->isValid());
    }

    #[Test]
    public function filledHoneypotMakesTheFormInvalid(): void
    {
        $this->stubTranslations();
        $form = $this->createForm(['issubmitted' => '1', 'winnie' => 'I am a bot']);

        self::assertFalse($form->isValid());
        self::assertSame(['error.honeypot'], $form->getErrors());

        $form = $this->createForm(['issubmitted' => '1', 'winnie' => 'I am a bot']);
        $form->disableHoneypot();
        self::assertTrue($form->isValid());
    }

    #[Test]
    public function protectionIsNotCheckedWhenNotSubmitted(): void
    {
        $form = $this->createFrontendForm(['csrftoken' => 'forged', 'winnie' => 'bot']);

        self::assertTrue($form->isValid());
        self::assertSame([], $form->getErrors());
    }

    #[Test]
    public function protectionErrorsAreAddedOnlyOnce(): void
    {
        $this->stubTranslations();
        $form = $this->createForm(['issubmitted' => '1', 'csrftoken' => 'forged', 'winnie' => 'bot']);

        $form->getErrors();
        $form->isValid();

        self::assertSame(['error.csrf', 'error.honeypot'], $form->getErrors());
    }

    /**
     * @param array<string, mixed> $body
     */
    private function createFrontendForm(array $body): Form
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->method('getSessionData')->willReturnCallback(fn(string $key): mixed => $this->session[$key] ?? null);
        $frontendUser->method('setAndSaveSessionData')->willReturnCallback(function (string $key, mixed $value): void {
            $this->session[$key] = $value;
        });
        $request = (new ServerRequest('https://example.com/', 'POST'))
            ->withParsedBody(['tx_test' => $body])
            ->withAttribute('frontend.user', $frontendUser)
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('language', new SiteLanguage(0, 'en_US.UTF-8', new Uri('/'), []));
        $form = new Form('tx_test', $request, $this->createStub(EventDispatcherInterface::class));
        $form->getErrorManager()->disableFlashMessage();
        return $form;
    }
}
