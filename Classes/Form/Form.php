<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Form;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Elements\Button;
use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Elements\Submit;
use Ameos\AmeosForm\Enum\Element;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Event\BindValueFromRequestEvent;
use Ameos\AmeosForm\Event\ValidFormEvent;
use Ameos\AmeosForm\Exception\RepositoryNotFoundException;
use Ameos\AmeosForm\Exception\RepositoryNotValidException;
use Ameos\AmeosForm\Utility\FormUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class Form
{
    /**
     * @var string $identifer identifier
     */
    protected $identifier;

    /**
     * @var array $elements elements
     */
    protected $elements = [];

    /**
     * @var bool $enableCrsftoken enable crsf token
     */
    protected $enableCsrftoken = true;

    /**
     * @var bool $enableHoneypot enable honey pot
     */
    protected $enableHoneypot = true;

    /**
     * @var bool $enableHoneypot store search in session
     */
    protected $storeSearchInSession = true;

    /**
     * @var bool $elementsConstraintsAreChecked true if elements constraints are checked
     */
    protected $elementsConstraintsAreChecked = false;

    /**
     * @var array $errorsByElement errorsByElement
     */
    protected $errorsByElement = [];

    /**
     * @var ErrorManager $errorManager
     */
    protected $errorManager;

    /**
     * @var ServerRequest $request
     */
    protected $request;

    /**
     * @var SearchableRepositoryInterface $repository
     */
    protected $repository = null;

    /**
     * @var AbstractEntity $entity
     */
    protected $entity = null;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $defaultClause;

    /**
     * @var array $bodyData
     */
    protected $bodyData;

    /**
     * @var array $uploadedFiles
     */
    protected $uploadedFiles;

    /**
     * @constuctor
     *
     * @param string $identifier form identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
        $this->errorManager = GeneralUtility::makeInstance(ErrorManager::class, $this);
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
        $this->context = GeneralUtility::makeInstance(Context::class);
        $this->request = $GLOBALS['TYPO3_REQUEST'];
        $this->defaultClause = [];

        $parsedBody = $this->request->getParsedBody();
        $uploadedFiles = $this->request->getUploadedFiles();

        $this->bodyData = isset($parsedBody[$identifier]) ? $parsedBody[$identifier] : [];
        $this->uploadedFiles = isset($uploadedFiles[$identifier]) ? $uploadedFiles[$identifier] : [];
    }

    /**
     * return identifier
     * @return string identifier
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * enable csrf token
     * @return self
     */
    public function enableCsrftoken(): self
    {
        $this->enableCsrftoken = true;

        return $this;
    }

    /**
     * disable csrf token
     * @return self
     */
    public function disableCsrftoken(): self
    {
        $this->enableCsrftoken = false;

        return $this;
    }

    /**
     * return TRUE if csrf token is enabled
     * @return bool
     */
    public function csrftokenIsEnabled(): bool
    {
        return $this->enableCsrftoken;
    }

    /**
     * enable honeypot
     * @return self
     */
    public function enableHoneypot(): self
    {
        $this->enableHoneypot = true;

        return $this;
    }

    /**
     * disable honeypot
     * @return self
     */
    public function disableHoneypot(): self
    {
        $this->enableHoneypot = false;

        return $this;
    }

    /**
     * return TRUE if honeypot is enabled
     * @return bool
     */
    public function honeypotIsEnabled(): bool
    {
        return $this->enableHoneypot;
    }

    /**
     * store search in session
     * @return self
     */
    public function storeSearchInSession(): self
    {
        $this->storeSearchInSession = true;

        return $this;
    }

    /**
     * disable search in session
     * @return self
     */
    public function disableSearchStoringInSession(): self
    {
        $this->storeSearchInSession = false;

        return $this;
    }

    /**
     * return TRUE if search is stored in session
     * @return bool
     */
    public function searchIsStoredInSession(): bool
    {
        return $this->storeSearchInSession;
    }

    /**
     * return elements
     * @return array<ElementInterface> elements
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * return elements
     * @return array<Submit|Button> elements
     */
    public function getSubmitterElements(): array
    {
        $submitters = [];
        foreach ($this->elements as $element) {
            if (
                is_a($element, Submit::class)
                || (is_a($element, Button::class) && $element->getType() === Button::TYPE_SUBMIT)
            ) {
                $submitters[] = $element;
            }
        }
        return $submitters;
    }

    /**
     * return element
     * @param string $name element name
     * @return ElementInterface|false
     */
    public function getElement(string $name): ElementInterface|false
    {
        return $this->hasElement($name) ? $this->elements[$name] : false;
    }

    /**
     * return TRUE if element exist
     * @param string $name element name
     * @return bool
     */
    public function hasElement(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * return element
     * alias getElement
     * @param string $name element name
     * @return ElementInterface|false
     */
    public function get(string $name): ElementInterface|false
    {
        return $this->getElement($name);
    }

    /**
     * return TRUE if element exist
     * alias hasElement
     * @param string $name element name
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->hasElement($name);
    }

    /**
     * add element fo the form
     *
     * @param   string  $type element type
     * @param   string  $name element name
     * @param   array  $configuration element configuration
     * @return  self
     */
    public function add(string $name, string $type = '', array $configuration = []): self
    {
        $absolutename = $this->identifier . '[' . $name . ']';

        /** @var ElementInterface */
        $element = GeneralUtility::makeInstance(
            FormUtility::getElementClassNameByType($type),
            $absolutename,
            $name,
            $configuration,
            $this
        );

        if ($this->entity !== null) {
            $method = 'get' . GeneralUtility::underscoredToUpperCamelCase($name);
            if (method_exists($this->entity, $method)) {
                $element->setValue($this->entity->$method());
            }
        }

        if (isset($this->bodyData[$name])) {
            $bindEvent = new BindValueFromRequestEvent($this, $element, $this->bodyData[$name]);
            $this->eventDispatcher->dispatch($bindEvent);

            $element->setValue($bindEvent->getValue());
        } elseif ($this->repository !== null && $this->storeSearchInSession()) {
            /** @var FrontendUserAuthentication */
            $frontendUser = $this->request->getAttribute('frontend.user');
            $clauses = $frontendUser->getSessionData('form-' . $this->getIdentifier() . '-clauses');

            if (is_array($clauses) && isset($clauses[$name])) {
                $element->setValue($clauses[$name]['elementvalue']);
            }
        }

        if ($type === Element::UPLOAD && isset($this->uploadedFiles[$name])) {
            $bindEvent = new BindValueFromRequestEvent($this, $element, $this->uploadedFiles[$name]);
            $this->eventDispatcher->dispatch($bindEvent);

            $element->setValue($bindEvent->getValue());
        }

        $this->elements[$name] = $element;

        return $this;
    }

    /**
     * bind request to the form
     *
     * @deprecated
     * @return self
     */
    public function bindRequest(): self
    {
        trigger_error('Bind request is not longer useful', E_USER_DEPRECATED);

        return $this;
    }

    /**
     * return true if submitted
     *
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return isset($this->bodyData['issubmitted']) && $this->bodyData['issubmitted'] == 1;
    }

    /**
     * return submitter
     * @return ElementInterface|false
     */
    public function getSubmitter(): ElementInterface|false
    {
        if ($this->isSubmitted()) {
            foreach ($this->getSubmitterElements() as $element) {
                if ($element->isClicked()) {
                    return $element;
                }
            }
        }

        return false;
    }

    /**
     * attach entity to update
     *
     * @param AbstractEntity $entity
     * @return self
     */
    public function attachEntity(AbstractEntity $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * return entity
     *
     * @return ?AbstractEntity
     */
    public function getAttachedEntity(): ?AbstractEntity
    {
        return $this->entity;
    }

    /**
     * update entity property
     *
     * @param string $property
     * @param mixed $value
     * @return self
     */
    public function updateEntityProperty(string $property, mixed $value): self
    {
        if ($this->entity !== null) {
            // todo reflection + cast ?
            $method = 'set' . GeneralUtility::underscoredToUpperCamelCase($property);
            if (method_exists($this->entity, $method)) {
                $this->entity->$method($value);
            }
        }

        return $this;
    }

    /**
     * attach repository for search
     *
     * @param SearchableRepositoryInterface $repository
     * @return self
     */
    public function attachRepository(SearchableRepositoryInterface $repository): self
    {
        $this->repository = $repository;

        return $this;
    }


    /**
     * add validator
     *
     * @param   string  $elementName element name
     * @param   string  $type validator type
     * @param   string  $message message
     * @param   array   $configuration configuration
     * @return  self
     * alias    addConstraint
     */
    public function validator($elementName, $type, $message, $configuration = [])
    {
        return $this->addConstraint($elementName, $type, $message, $configuration);
    }

    /**
     * add element constraint
     *
     * @param   string  $elementName element name
     * @param   string  $type constraint type
     * @param   string  $message message
     * @param   array   $configuration configuration
     * @return  self
     */
    public function addConstraint($elementName, $type, $message, $configuration = [])
    {
        if ($this->has($elementName)) {
            $constraint = GeneralUtility::makeInstance(
                FormUtility::getConstrainClassNameByType($type),
                $message,
                $configuration,
                $this->getElement($elementName),
                $this
            );

            $this->get($elementName)->addConstraint($constraint);
        }

        return $this;
    }

    /**
     * add where clause
     *
     * @param array $clause
     * @return self
     */
    public function addWhereClause($clause): self
    {
        $this->defaultClause[] = $clause;

        return $this;
    }

    /**
     * return true if the form is valide
     *
     * @return bool true if is a valid form
     */
    public function isValid(): bool
    {
        $isValid = $this->errorManager->isValid();
        if ($isValid) {
            $this->eventDispatcher->dispatch(new ValidFormEvent($this));
        }

        return $isValid;
    }

    /**
     * Return errors
     *
     * @return array errors
     */
    public function getErrors(): array
    {
        return $this->getErrorManager()->getFlatErrors();
    }

    /**
     * return results of seach
     *
     * @param ?string $orderby
     * @param string $direction
     * @return iterable
     */
    public function getResults(?string $orderby = null, string $direction = 'ASC'): iterable
    {
        if ($this->repository === null) {
            throw new RepositoryNotFoundException('Repository not found');
        }

        if (!is_a($this->repository, SearchableRepositoryInterface::class)) {
            throw new RepositoryNotValidException('Repository must implements ' . SearchableRepositoryInterface::class);
        }

        $clauses = [];
        foreach ($this->elements as $element) {
            if ($element->isSearchable()) {
                if ($element->getValue() == '') {
                    unset($clauses[$element->getName()]);
                }
                if (($clause = $element->getClause()) !== false) {
                    $clauses[$clause['elementname']] = $clause;
                }
            }
        }

        /** @var FrontendUserAuthentication */
        $frontendUser = $this->request->getAttribute('frontend.user');
        $frontendUser->setAndSaveSessionData('form-' . $this->getIdentifier() . '-clauses', $clauses);

        $clauses = array_merge($clauses, $this->defaultClause);
        return $this->repository->findByClausesArray($clauses, $orderby, $direction);
    }

    /**
     * return error manager
     *
     * @return ErrorManager
     */
    public function getErrorManager(): ErrorManager
    {
        return $this->errorManager;
    }

    /**
     * return bodyData
     *
     * @return array
     */
    public function getBodyData(): array
    {
        return $this->bodyData;
    }
}
