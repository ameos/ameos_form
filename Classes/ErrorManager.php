<?php

declare(strict_types=1);

namespace Ameos\AmeosForm;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ErrorManager
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var
     */
    private $verifiedElements = [];

    /**
     * @var bool
     */
    private $flashMessagesEnabled = true;

    /**
     * constuctor
     *
     * @param Form $form
     */
    public function __construct(private Form $form)
    {
    }

    /**
     * disable flash message
     *
     * @return self
     */
    public function disableFlashMessage(): self
    {
        $this->flashMessagesEnabled = false;
        return $this;
    }

    /**
     * add an error
     *
     * @param string $message
     * @param string $element
     * @return self
     */
    public function add(string $message, string $element): self
    {
        if (isset($this->errors[$element])) {
            $this->errors[$element] = [];
        }

        $this->errors[$element][] = $message;

        if ($this->flashMessagesEnabled) {
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $flashMessageService
                ->getMessageQueueByIdentifier('extbase.flashmessages.' . $this->form->getIdentifier())
                ->addMessage(new FlashMessage($message, '', ContextualFeedbackSeverity::ERROR, true));
        }

        return $this;
    }

    /**
     * return errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        $this->determineErrors();

        return $this->errors;
    }

    /**
     * return errors flatten
     *
     * @return array
     */
    public function getFlatErrors(): array
    {
        $this->determineErrors();

        $errors = [];
        foreach ($this->errors as $elementErrors) {
            $errors = array_merge($errors, $elementErrors);
        }
        return $errors;
    }

    /**
     * return errors for $element
     *
     * @param ElementInterface $element
     * @return array
     */
    public function getErrorsFor(ElementInterface $element): array
    {
        $this->determineErrors();

        $name = $element->getName();
        return isset($this->errors[$name]) ? $this->errors[$name] : [];
    }

    /**
     * return true if no error
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $this->determineErrors();

        return empty($this->errors);
    }

    /**
     * return true if $element no error
     *
     * @param ElementInterface $element
     * @return bool
     */
    public function elementIsValid(ElementInterface $element): bool
    {
        $name = $element->getName();
        return !isset($this->errors[$name]) || empty($this->errors[$name]);
    }

    /**
     * determine errors
     */
    protected function determineErrors()
    {
        foreach ($this->form->getElements() as $element) {
            $this->determineErrorsForElement($element);
        }
    }

    /**
     * determine errors for an element
     * @param Ameos\AmeosForm\Elements\ElementAbstract|string $element element
     */
    protected function determineErrorsForElement($element)
    {
        $elementName = is_string($element) ? $element : $element->getName();

        if (!in_array($elementName, $this->verifiedElements)) {
            $this->form->get($elementName)->determineErrors();
            $this->verifiedElements[] = $elementName;
        }
    }
}
