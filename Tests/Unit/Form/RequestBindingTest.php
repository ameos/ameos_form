<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Form;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Service\FormService;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use Ameos\AmeosForm\Tests\Unit\Fixtures\TypedMovie;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\UploadedFile;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request as ExtbaseRequest;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Binding of request values and entity values
 */
final class RequestBindingTest extends AbstractUnitTestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    #[Test]
    public function requestValuesAreCastToTypedEntitySetters(): void
    {
        $movie = $this->createMovie();
        $form = $this->createForm([
            'issubmitted' => '1',
            'title' => 'Alien',
            'year' => '1979',
            'favorite' => '1',
            'score' => '4.5',
            'release_date' => '',
            'tags' => ['classic', 'horror'],
            'comment' => '',
        ]);
        $form->attachEntity($movie)
            ->add('title', 'text')
            ->add('year', 'number')
            ->add('favorite', 'checksingle')
            ->add('score', 'text')
            ->add('release_date', 'textdate')
            ->add('tags', 'checkbox', ['items' => ['classic' => 'Classic', 'horror' => 'Horror']])
            ->add('comment', 'text');

        self::assertSame('Alien', $movie->title);
        self::assertSame(1979, $movie->year);
        self::assertTrue($movie->favorite);
        self::assertSame(4.5, $movie->score);
        self::assertNull($movie->releaseDate);
        self::assertSame('classic,horror', $movie->tags);
        self::assertNull($movie->comment, 'empty string becomes NULL for a nullable parameter');
    }

    #[Test]
    public function dateIsGivenToDateTimeSetters(): void
    {
        $movie = $this->createMovie();
        $form = $this->createForm(['issubmitted' => '1', 'release_date' => '2001-04-25', 'immutable_date' => '2002-01-02']);
        $form->attachEntity($movie)->add('release_date', 'textdate')->add('immutable_date', 'textdate');

        self::assertSame('2001-04-25', $movie->releaseDate?->format('Y-m-d'));
        self::assertSame('2002-01-02', $movie->immutableDate?->format('Y-m-d'));
    }

    #[Test]
    public function entityValueIsNotWrittenBackWhenBound(): void
    {
        $movie = $this->createMovie();
        $movie->releaseDate = new \DateTime('2001-04-25 00:00:00');
        $form = $this->createForm();
        // input=timestamp converts the date to an int, it must not be given back to the DateTime setter
        $form->attachEntity($movie)->add('release_date', 'textdate', ['input' => 'timestamp']);

        self::assertSame(0, $movie->setterCalls);
        self::assertSame($movie->releaseDate->getTimestamp(), $this->element($form, 'release_date')->getValue());
    }

    #[Test]
    public function uncheckedElementsAreEmptiedWhenSubmitted(): void
    {
        $movie = $this->createMovie();
        $movie->favorite = true;
        $movie->tags = 'classic';
        // unchecked checkboxes and multiple select without selection are not sent by the browser
        $form = $this->createForm(['issubmitted' => '1', 'title' => 'Alien']);
        $form->attachEntity($movie)
            ->add('title', 'text')
            ->add('favorite', 'checksingle')
            ->add('tags', 'checkbox', ['items' => ['classic' => 'Classic']])
            ->add('languages', 'dropdown', ['items' => ['fr' => 'French'], 'multiple' => true, 'defaultValue' => 'fr']);

        self::assertFalse($movie->favorite);
        self::assertSame('', $movie->tags);
        self::assertSame([], $this->element($form, 'tags')->getValue());
        self::assertSame('', $this->element($form, 'favorite')->getValue());
        self::assertSame('', $this->element($form, 'languages')->getValue());
    }

    #[Test]
    public function missingValuesAreKeptWhenNotSubmittedOrDisabled(): void
    {
        $movie = $this->createMovie();
        $movie->favorite = true;
        $form = $this->createForm();
        $form->attachEntity($movie)->add('favorite', 'checksingle')->add('select', 'dropdown', ['items' => ['a' => 'A'], 'defaultValue' => 'a']);
        self::assertTrue($movie->favorite);
        self::assertSame('1', $this->stringValue($this->element($form, 'favorite')));

        $form = $this->createForm(['issubmitted' => '1']);
        $form->attachEntity($movie)
            ->add('favorite', 'checksingle', ['disabled' => true])
            ->add('select', 'dropdown', ['items' => ['a' => 'A'], 'defaultValue' => 'a'])
            ->add('other', 'text', ['defaultValue' => 'default']);
        self::assertTrue($movie->favorite);
        self::assertSame('a', $this->element($form, 'select')->getValue());
        self::assertSame('default', $this->element($form, 'other')->getValue());
    }

    #[Test]
    public function searchSessionIsNotUsedWhenSubmitted(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->method('getSessionData')->willReturn(['tags' => ['elementvalue' => 'a,b'], 'title' => ['elementvalue' => 'stored']]);
        $request = (new ServerRequest('https://example.com/', 'POST'))
            ->withParsedBody(['tx_test' => ['issubmitted' => '1', 'csrftoken' => 'x']])
            ->withAttribute('frontend.user', $frontendUser);

        $form = (new FormService($this->createStub(EventDispatcherInterface::class)))
            ->create('tx_test', $this->createStub(SearchableRepositoryInterface::class), $request);
        $form->add('tags', 'checkbox', ['items' => ['a' => 'A', 'b' => 'B']])->add('title', 'text');

        self::assertSame([], $this->element($form, 'tags')->getValue());
        self::assertSame('', $this->element($form, 'title')->getValue());
    }

    #[Test]
    public function searchIsNotStoredInSessionWhenDisabled(): void
    {
        $frontendUser = $this->createMock(FrontendUserAuthentication::class);
        $frontendUser->expects(self::never())->method('setAndSaveSessionData');
        $repository = $this->createStub(SearchableRepositoryInterface::class);
        $repository->method('findByClausesArray')->willReturn([]);
        $request = (new ServerRequest('https://example.com/'))->withAttribute('frontend.user', $frontendUser);

        $form = (new FormService($this->createStub(EventDispatcherInterface::class)))->create('tx_test', $repository, $request);
        $form->disableSearchStoringInSession()->add('title', 'text', ['defaultValue' => 'foo']);

        self::assertSame([], $form->getResults());
    }

    #[Test]
    public function zeroIsAValidSearchValue(): void
    {
        $form = $this->createForm(['title' => '0', 'genre' => '0']);
        $form->add('title', 'text')->add('genre', 'dropdown', ['items' => ['0' => 'None', '1' => 'One']]);

        self::assertSame('%0%', $this->clause($this->element($form, 'title'))['value']);
        self::assertSame('0', $this->clause($this->element($form, 'genre'))['value']);
    }

    #[Test]
    public function uploadedFilesAreReadFromExtbaseRequestOfThePluginNamespace(): void
    {
        $file = new UploadedFile('php://memory', 3, UPLOAD_ERR_OK, 'cv.pdf');
        // Extbase requests (TYPO3 >= 13) only contain the files of the plugin namespace, without the namespace
        $request = $this->createExtbaseRequest(['cv' => ['upload' => [$file]]]);

        $values = $this->bindUpload(new Form('tx_myext_plugin', $request, $this->createBindEventRecorder($recorded)));

        self::assertSame([['upload' => [$file], 'temporary' => []]], $recorded);
        self::assertSame([], $values, 'form identifier different from the plugin namespace');
    }

    #[Test]
    public function uploadedFilesOfAnotherNamespaceAreReadFromTheGlobalRequest(): void
    {
        $file = new UploadedFile('php://memory', 3, UPLOAD_ERR_OK, 'cv.pdf');
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://example.com/', 'POST'))
            ->withUploadedFiles(['tx_other' => ['cv' => ['upload' => [$file]]]]);
        $request = $this->createExtbaseRequest([]);

        $this->bindUpload(new Form('tx_other', $request, $this->createBindEventRecorder($recorded)));

        self::assertSame([['upload' => [$file], 'temporary' => []]], $recorded);
    }

    /**
     * Binds an upload element to "tx_myext_plugin" and "tx_other" forms, returns the values bound to the other form
     *
     * @return list<mixed>
     */
    private function bindUpload(Form $form): array
    {
        $form->add('cv', 'upload', ['directory' => 'fileadmin/test']);
        $other = new Form('tx_unknown', $form->getCurrentRequest(), $this->createBindEventRecorder($recorded));
        $other->add('cv', 'upload', ['directory' => 'fileadmin/test']);
        return $recorded;
    }

    /**
     * @param array<string, mixed> $uploadedFiles
     */
    private function createExtbaseRequest(array $uploadedFiles): ServerRequestInterface
    {
        $parameters = (new ExtbaseRequestParameters())
            ->setControllerExtensionName('MyExt')
            ->setPluginName('Plugin')
            ->setUploadedFiles($uploadedFiles);
        return new ExtbaseRequest(
            (new ServerRequest('https://example.com/', 'POST'))
                ->withParsedBody(['tx_myext_plugin' => ['issubmitted' => '1']])
                ->withAttribute('extbase', $parameters),
        );
    }

    /**
     * @param-out list<mixed> $recorded values given to the BindValueFromRequestEvent
     */
    private function createBindEventRecorder(mixed &$recorded): EventDispatcherInterface
    {
        $recorded = [];
        $eventDispatcher = $this->createStub(EventDispatcherInterface::class);
        $eventDispatcher->method('dispatch')->willReturnCallback(static function (object $event) use (&$recorded): object {
            if ($event instanceof BindValueFromRequestEvent) {
                $recorded[] = $event->getValue();
            }
            return $event;
        });
        return $eventDispatcher;
    }

    /**
     * @return array<string, mixed>
     */
    private function clause(ElementInterface $element): array
    {
        $clause = $element->getClause();
        self::assertIsArray($clause);
        return $clause;
    }

    private function stringValue(ElementInterface $element): string
    {
        $value = $element->getValue();
        return is_scalar($value) ? (string) $value : '';
    }

    private function createMovie(): TypedMovie
    {
        return new TypedMovie();
    }
}
