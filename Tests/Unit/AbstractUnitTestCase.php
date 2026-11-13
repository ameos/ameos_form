<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

abstract class AbstractUnitTestCase extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    /**
     * Form stub with a real error manager (flash messages disabled)
     *
     * @param array<string, ElementInterface> $elements
     */
    protected function createFormStub(bool $isSubmitted = false, array $elements = []): Form
    {
        $form = $this->createStub(Form::class);
        $errorManager = (new ErrorManager($form))->disableFlashMessage();
        $form->method('getErrorManager')->willReturn($errorManager);
        $form->method('isSubmitted')->willReturn($isSubmitted);
        $form->method('getElements')->willReturnCallback(static fn(): array => $elements);
        $form->method('getIdentifier')->willReturn('tx_test');
        return $form;
    }

    /**
     * Real form bound to a frontend POST request
     *
     * @param array<string, mixed> $body
     * @param array<string, mixed> $files
     */
    protected function createForm(array $body = [], array $files = [], ?EventDispatcherInterface $eventDispatcher = null): Form
    {
        // as rendered by the form ViewHelper: token "notoken" without frontend user
        if (isset($body['issubmitted']) && !array_key_exists('csrftoken', $body)) {
            $body['csrftoken'] = 'notoken';
        }
        $request = (new ServerRequest('https://example.com/form', 'POST'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('language', new SiteLanguage(0, 'en_US.UTF-8', new Uri('/'), []))
            ->withParsedBody($body === [] ? [] : ['tx_test' => $body])
            ->withUploadedFiles($files === [] ? [] : ['tx_test' => $files]);
        $form = new Form('tx_test', $request, $eventDispatcher ?? $this->createStub(EventDispatcherInterface::class));
        $form->getErrorManager()->disableFlashMessage();
        return $form;
    }

    /**
     * @template T of ElementInterface
     * @param class-string<T> $className
     * @return T
     */
    protected function element(Form $form, string $name, string $className = ElementInterface::class): ElementInterface
    {
        $element = $form->getElement($name);
        self::assertInstanceOf($className, $element);
        return $element;
    }

    /**
     * LocalizationUtility::translate() needs the LanguageServiceFactory from the DI container
     */
    protected function stubTranslations(int $calls = 10): void
    {
        $languageService = $this->createStub(LanguageService::class);
        $languageService->method('translate')->willReturnCallback(
            static fn(string $key): string => match ($key) {
                'fileSizeError' => 'The size of the file "%file_name%" exceeds the allowed limit of %max_size%.',
                'fileNameError' => 'The file "%file_name%" is not allowed.',
                default => $key,
            },
        );
        $languageServiceFactory = $this->createStub(LanguageServiceFactory::class);
        $languageServiceFactory->method('create')->willReturn($languageService);
        for ($i = 0; $i < $calls; $i++) {
            GeneralUtility::addInstance(LanguageServiceFactory::class, $languageServiceFactory);
        }
    }
}
