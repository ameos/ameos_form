<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Form;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Elements\Button;
use Ameos\AmeosForm\Elements\ElementAbstract;
use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Elements\Submit;
use Ameos\AmeosForm\Enum\Constraint;
use Ameos\AmeosForm\Enum\Element;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use Ameos\AmeosForm\Event\ValidFormEvent;
use Ameos\AmeosForm\Exception\RepositoryNotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\RequestInterface as ExtbaseRequestInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Form model: holds the elements, binds the submitted values and validates them.
 * Not final: it is mocked in unit tests and may be extended or XCLASSed by projects.
 */
class Form
{
    /** Key used in the error manager for errors not related to an element (CSRF token, honeypot) */
    public const string FORM_ERROR_KEY = '__form';
    private const string NO_CSRF_TOKEN = 'notoken';

    protected readonly ServerRequestInterface $request;
    protected readonly EventDispatcherInterface $eventDispatcher;
    protected readonly ErrorManager $errorManager;

    /** @var array<string, ElementInterface> */
    protected array $elements = [];
    protected bool $enableCsrftoken = true;
    protected bool $enableHoneypot = true;
    protected bool $storeSearchInSession = true;
    protected ?SearchableRepositoryInterface $repository = null;
    protected ?AbstractEntity $entity = null;
    /** @var list<array<string, mixed>> */
    protected array $defaultClause = [];
    /** @var array<array-key, mixed> */
    protected array $bodyData;
    /** @var array<array-key, mixed> */
    protected array $uploadedFiles;
    protected ?bool $validity = null;
    /** true while the value read from the attached entity is bound (no need to write it back) */
    protected bool $isBindingEntity = false;
    protected bool $protectionVerified = false;

    public function __construct(
        protected readonly string $identifier,
        ?ServerRequestInterface $request = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ) {
        $this->request = $request ?? self::getGlobalRequest();
        $this->eventDispatcher = $eventDispatcher ?? GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $this->errorManager = GeneralUtility::makeInstance(ErrorManager::class, $this);

        $parsedBody = $this->request->getParsedBody();
        $this->bodyData = is_array($parsedBody) && is_array($parsedBody[$identifier] ?? null)
            ? $parsedBody[$identifier]
            : [];
        $this->uploadedFiles = $this->readUploadedFiles();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function enableCsrftoken(): self
    {
        $this->enableCsrftoken = true;
        return $this;
    }

    public function disableCsrftoken(): self
    {
        $this->enableCsrftoken = false;
        return $this;
    }

    public function csrftokenIsEnabled(): bool
    {
        return $this->enableCsrftoken;
    }

    public function enableHoneypot(): self
    {
        $this->enableHoneypot = true;
        return $this;
    }

    public function disableHoneypot(): self
    {
        $this->enableHoneypot = false;
        return $this;
    }

    public function honeypotIsEnabled(): bool
    {
        return $this->enableHoneypot;
    }

    public function storeSearchInSession(): self
    {
        $this->storeSearchInSession = true;
        return $this;
    }

    public function disableSearchStoringInSession(): self
    {
        $this->storeSearchInSession = false;
        return $this;
    }

    public function searchIsStoredInSession(): bool
    {
        return $this->storeSearchInSession;
    }

    /**
     * @return array<string, ElementInterface>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @return list<Submit|Button>
     */
    public function getSubmitterElements(): array
    {
        $submitters = [];
        foreach ($this->elements as $element) {
            if ($element instanceof Submit || ($element instanceof Button && $element->getType() === Button::TYPE_SUBMIT)) {
                $submitters[] = $element;
            }
        }
        return $submitters;
    }

    public function getElement(string $name): ElementInterface|false
    {
        return $this->elements[$name] ?? false;
    }

    public function hasElement(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * Alias of getElement()
     */
    public function get(string $name): ElementInterface|false
    {
        return $this->getElement($name);
    }

    /**
     * Alias of hasElement()
     */
    public function has(string $name): bool
    {
        return $this->hasElement($name);
    }

    /**
     * Add an element to the form
     *
     * @param string $type one of the Element::* constants
     * @param array<string, mixed> $configuration
     */
    public function add(string $name, string $type = '', array $configuration = []): self
    {
        /** @var ElementInterface $element */
        $element = GeneralUtility::makeInstance(
            Element::getElementClassName($type),
            $this->identifier . '[' . $name . ']',
            $name,
            $configuration,
            $this,
        );

        $this->isBindingEntity = true;
        try {
            $this->bindEntityValue($element, $name);
        } finally {
            $this->isBindingEntity = false;
        }
        if ($type === Element::UPLOAD) {
            $this->bindUploadValue($element, $name);
        } else {
            $this->bindRequestValue($element, $name);
        }

        $this->elements[$name] = $element;

        return $this;
    }

    public function getCurrentRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function isSubmitted(): bool
    {
        $isSubmitted = $this->bodyData['issubmitted'] ?? null;
        return is_scalar($isSubmitted) && (int) $isSubmitted === 1;
    }

    public function getSubmitter(): Submit|Button|false
    {
        if (!$this->isSubmitted()) {
            return false;
        }
        foreach ($this->getSubmitterElements() as $element) {
            if ($element->isClicked()) {
                return $element;
            }
        }
        return false;
    }

    public function attachEntity(AbstractEntity $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    public function getAttachedEntity(): ?AbstractEntity
    {
        return $this->entity;
    }

    /**
     * Call the entity setter. Request values (strings) are cast to the scalar type of the setter parameter
     * (int, float, bool, string), an empty string becomes NULL for nullable parameters.
     */
    public function updateEntityProperty(string $property, mixed $value): self
    {
        if ($this->entity === null || $this->isBindingEntity) {
            return $this;
        }
        $method = 'set' . GeneralUtility::underscoredToUpperCamelCase($property);
        $setter = [$this->entity, $method];
        if (is_callable($setter)) {
            $setter($this->castForSetter(new \ReflectionMethod($this->entity, $method), $value));
        }
        return $this;
    }

    public function attachRepository(SearchableRepositoryInterface $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Alias of addConstraint()
     *
     * @param array<string, mixed> $configuration
     */
    public function validator(string $elementName, string $type, string $message, array $configuration = []): self
    {
        return $this->addConstraint($elementName, $type, $message, $configuration);
    }

    /**
     * Add a constraint to an element
     *
     * @param string $type one of the Constraint::* constants
     * @param array<string, mixed> $configuration
     */
    public function addConstraint(string $elementName, string $type, string $message, array $configuration = []): self
    {
        $element = $this->elements[$elementName] ?? null;
        if ($element !== null) {
            $element->addConstraint(GeneralUtility::makeInstance(
                Constraint::getConstraintClassName($type),
                $message,
                $configuration,
                $element,
                $this,
            ));
        }
        return $this;
    }

    /**
     * @param array<string, mixed> $clause
     */
    public function addWhereClause(array $clause): self
    {
        $this->defaultClause[] = $clause;
        return $this;
    }

    /**
     * Return TRUE if the form is valid. ValidFormEvent is dispatched only once, on the first valid check
     * of a submitted form.
     */
    public function isValid(): bool
    {
        if ($this->validity === null) {
            $this->validity = $this->errorManager->isValid();
            if ($this->validity && $this->isSubmitted()) {
                $this->eventDispatcher->dispatch(new ValidFormEvent($this));
            }
        }
        return $this->validity;
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->getErrorManager()->getFlatErrors();
    }

    /**
     * Return the search results of the attached repository, filtered with the searchable elements
     *
     * @return iterable<mixed>
     * @throws RepositoryNotFoundException
     */
    public function getResults(?string $orderby = null, string $direction = 'ASC'): iterable
    {
        if ($this->repository === null) {
            throw new RepositoryNotFoundException('Repository not found', 1731340802);
        }

        $clauses = [];
        foreach ($this->elements as $element) {
            $clause = $element->isSearchable() ? $element->getClause() : false;
            if ($clause !== false) {
                $clauses[$element->getName()] = $clause;
            }
        }

        if ($this->searchIsStoredInSession()) {
            $this->getFrontendUser()?->setAndSaveSessionData($this->getSessionClausesKey(), $clauses);
        }

        return $this->repository->findByClausesArray(
            array_merge(array_values($clauses), $this->defaultClause),
            $orderby,
            $direction,
        );
    }

    /**
     * CSRF token of the form, stored in the frontend user session ("notoken" without frontend user)
     */
    public function getCsrfToken(): string
    {
        $frontendUser = $this->getFrontendUser();
        if ($frontendUser === null) {
            return self::NO_CSRF_TOKEN;
        }
        $token = $frontendUser->getSessionData($this->getSessionCsrfTokenKey());
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $frontendUser->setAndSaveSessionData($this->getSessionCsrfTokenKey(), $token);
        }
        return $token;
    }

    /**
     * Check the CSRF token and the honeypot of a submitted form, errors are added to the error manager
     * (with the FORM_ERROR_KEY key). Called once by the error manager.
     *
     * @internal
     */
    public function verifyProtection(): void
    {
        if ($this->protectionVerified || !$this->isSubmitted()) {
            return;
        }
        $this->protectionVerified = true;

        if ($this->enableCsrftoken && !$this->isCsrfTokenValid()) {
            $this->errorManager->add($this->translate('error.csrf', 'The form has expired, please submit it again.'), self::FORM_ERROR_KEY);
        }
        $honeypot = $this->bodyData['winnie'] ?? '';
        if ($this->enableHoneypot && (!is_string($honeypot) || $honeypot !== '')) {
            $this->errorManager->add($this->translate('error.honeypot', 'The form could not be sent.'), self::FORM_ERROR_KEY);
        }
    }

    public function getErrorManager(): ErrorManager
    {
        return $this->errorManager;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getBodyData(): array
    {
        return $this->bodyData;
    }

    /**
     * Render all elements (used by the CompiledForm ViewHelper)
     */
    public function toHtml(): string
    {
        return implode('', array_map(static fn(ElementInterface $element): string => $element->toHtml(), $this->elements));
    }

    protected function bindEntityValue(ElementInterface $element, string $name): void
    {
        if ($this->entity === null) {
            return;
        }
        $getter = [$this->entity, 'get' . GeneralUtility::underscoredToUpperCamelCase($name)];
        if (is_callable($getter)) {
            $element->setValue($getter());
        }
    }

    protected function bindRequestValue(ElementInterface $element, string $name): void
    {
        if (array_key_exists($name, $this->bodyData)) {
            $element->setValue($this->dispatchBindEvent($element, $this->bodyData[$name]));
            return;
        }

        if ($this->isSubmitted()) {
            // e.g. unchecked checkboxes are not sent by the browser: the element is emptied
            if ($element instanceof ElementAbstract && $element->isOmittedFromRequestWhenEmpty()) {
                $element->setValue('');
            }
            return;
        }

        if ($this->repository === null || !$this->searchIsStoredInSession()) {
            return;
        }
        $clauses = $this->getFrontendUser()?->getSessionData($this->getSessionClausesKey());
        if (is_array($clauses) && is_array($clauses[$name] ?? null) && array_key_exists('elementvalue', $clauses[$name])) {
            $element->setValue($clauses[$name]['elementvalue']);
        }
    }

    /**
     * Uploaded files come from the request files, already uploaded (temporary) files from the request body
     */
    protected function bindUploadValue(ElementInterface $element, string $name): void
    {
        $files = is_array($this->uploadedFiles[$name] ?? null) ? $this->uploadedFiles[$name] : [];
        $body = is_array($this->bodyData[$name] ?? null) ? $this->bodyData[$name] : [];
        if ($files === [] && $body === []) {
            return;
        }
        $value = [
            'upload' => is_array($files['upload'] ?? null) ? $files['upload'] : [],
            'temporary' => is_array($body['temporary'] ?? null) ? $body['temporary'] : [],
        ];
        $element->setValue($this->dispatchBindEvent($element, $value));
    }

    protected function dispatchBindEvent(ElementInterface $element, mixed $value): mixed
    {
        $event = new BindValueFromRequestEvent($this, $element, $value);
        $this->eventDispatcher->dispatch($event);
        return $event->getValue();
    }

    protected function getFrontendUser(): ?FrontendUserAuthentication
    {
        $frontendUser = $this->request->getAttribute('frontend.user');
        return $frontendUser instanceof FrontendUserAuthentication ? $frontendUser : null;
    }

    protected function isCsrfTokenValid(): bool
    {
        $submittedToken = $this->bodyData['csrftoken'] ?? null;
        if (!is_string($submittedToken) || $submittedToken === '') {
            return false;
        }
        $frontendUser = $this->getFrontendUser();
        $expectedToken = $frontendUser === null
            ? self::NO_CSRF_TOKEN
            : $frontendUser->getSessionData($this->getSessionCsrfTokenKey());
        return is_string($expectedToken) && $expectedToken !== '' && hash_equals($expectedToken, $submittedToken);
    }

    protected function getSessionCsrfTokenKey(): string
    {
        return $this->identifier . '-csrftoken';
    }

    /**
     * Cast a value to the type of the first parameter of the entity setter (strict_types is enabled here)
     */
    protected function castForSetter(\ReflectionMethod $setter, mixed $value): mixed
    {
        $type = ($setter->getParameters()[0] ?? null)?->getType();
        if (!$type instanceof \ReflectionNamedType) {
            return $value;
        }
        if ($type->allowsNull() && ($value === null || $value === '')) {
            return null;
        }
        $isScalar = is_scalar($value) || $value === null;
        return match ($type->getName()) {
            'int' => $isScalar ? (int) $value : $value,
            'float' => $isScalar ? (float) $value : $value,
            'bool' => $isScalar ? (bool) $value : $value,
            'string' => match (true) {
                $isScalar, $value instanceof \Stringable => (string) $value,
                is_array($value) => implode(',', array_map(static fn(mixed $item): string => is_scalar($item) ? (string) $item : '', $value)),
                default => $value,
            },
            \DateTimeImmutable::class => $value instanceof \DateTime ? \DateTimeImmutable::createFromMutable($value) : $value,
            \DateTime::class => $value instanceof \DateTimeImmutable ? \DateTime::createFromImmutable($value) : $value,
            default => $value,
        };
    }

    protected function translate(string $key, string $default): string
    {
        return LocalizationUtility::translate($key, 'ameos_form', null, null, $this->request) ?? $default;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function readUploadedFiles(): array
    {
        $files = $this->request->getUploadedFiles()[$this->identifier] ?? null;
        if (is_array($files)) {
            return $files;
        }
        if (!$this->request instanceof ExtbaseRequestInterface) {
            return [];
        }
        // Since TYPO3 13, an Extbase request only contains the uploaded files of the plugin namespace, without it
        $globalRequest = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($globalRequest instanceof ServerRequestInterface && !$globalRequest instanceof ExtbaseRequestInterface) {
            $files = $globalRequest->getUploadedFiles()[$this->identifier] ?? null;
            if (is_array($files)) {
                return $files;
            }
        }
        $pluginNamespace = strtolower('tx_' . $this->request->getControllerExtensionName() . '_' . $this->request->getPluginName());
        return $pluginNamespace === $this->identifier ? $this->request->getUploadedFiles() : [];
    }

    protected function getSessionClausesKey(): string
    {
        return 'form-' . $this->identifier . '-clauses';
    }

    private static function getGlobalRequest(): ServerRequestInterface
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            throw new \RuntimeException('No request available, pass it to the form constructor', 1731340803);
        }
        return $request;
    }
}
