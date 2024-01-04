<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Form;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Elements\Button;
use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Elements\Submit;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Exception\RepositoryNotFoundException;
use Ameos\AmeosForm\Exception\RepositoryNotValidException;
use Ameos\AmeosForm\Utility\FormUtility;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Repository;

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
     * @var Repository $repository
     */
    protected $repository = null;

    /**
     * @var AbstractEntity $entity
     */
    protected $entity = null;

    /**
     * @var array $bodyData
     */
    protected $bodyData;

    /**
     * @constuctor
     *
     * @param string $identifier form identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
        $this->errorManager = GeneralUtility::makeInstance(ErrorManager::class, $this);
        $this->request = $GLOBALS['TYPO3_REQUEST'];

        $parsedBody = $this->request->getParsedBody();
        $this->bodyData = isset($parsedBody[$identifier]) ? $parsedBody[$identifier] : [];
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
     * return elements
     * @return array elements
     */
    public function getElements(): array
    {
        return $this->elements;
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
     * @param   string  $configuration element configuration
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
            $element->setValue($this->bodyData[$name]);
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
        // todo trigger deprecated

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
            foreach ($this->getElements() as $element) {
                if ($element->isClicked()
                    && (
                        is_a($element, Submit::class)
                        || (is_a($element, Button::class) && $element->getType() === Button::TYPE_SUBMIT)
                    )
                ) {
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
     * @param Repository $repository
     * @return self
     */
    public function attachRepository(Repository $repository): self
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
     * @return  \Ameos\AmeosForm\Form this
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
     * @return  \Ameos\AmeosForm\Form this
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
     * return true if the form is valide
     *
     * @return bool true if is a valid form
     */
    public function isValid(): bool
    {
        /**if ($this->errorManager->isValid()) {
            Events::getInstance($this->getIdentifier())->trigger('form_is_valid');
        }*/

        return $this->errorManager->isValid();
    }

    /**
     * Return errors
     *
     * @return array errors
     */
    public function getErrors(): array
    {
        // todo
        return [];
    }

    /**
     * return results of seach
     *
     * @param ?string $orderby
     * @param ?string $direction
     * @return iterable
     */
    public function getResults(?string $orderby = null, string $direction = 'ASC'): iterable
    {
        // todo
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

        //$clauses = array_merge($this->clauses, $this->defaultClause); TODO
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
