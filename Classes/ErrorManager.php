<?php

declare(strict_types=1);

namespace Ameos\AmeosForm;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Collects the validation errors of a form. Not final: it is mocked in unit tests.
 */
class ErrorManager
{
    /** @var array<string, list<string>> errors by element name */
    private array $errors = [];
    /** @var array<string, true> */
    private array $verifiedElements = [];
    private bool $flashMessagesEnabled = true;

    public function __construct(private readonly Form $form) {}

    public function disableFlashMessage(): self
    {
        $this->flashMessagesEnabled = false;
        return $this;
    }

    public function add(string $message, string $element): self
    {
        $this->errors[$element][] = $message;

        if ($this->flashMessagesEnabled) {
            GeneralUtility::makeInstance(FlashMessageService::class)
                ->getMessageQueueByIdentifier('extbase.flashmessages.' . $this->form->getIdentifier())
                ->addMessage(new FlashMessage($message, '', ContextualFeedbackSeverity::ERROR, true));
        }

        return $this;
    }

    /**
     * @return array<string, list<string>>
     */
    public function getErrors(): array
    {
        $this->determineErrors();
        return $this->errors;
    }

    /**
     * @return list<string>
     */
    public function getFlatErrors(): array
    {
        $this->determineErrors();
        return array_merge(...array_values($this->errors));
    }

    /**
     * @return list<string>
     */
    public function getErrorsFor(ElementInterface $element): array
    {
        $this->determineErrors();
        return $this->errors[$element->getName()] ?? [];
    }

    public function isValid(): bool
    {
        $this->determineErrors();
        return $this->errors === [];
    }

    public function elementIsValid(ElementInterface $element): bool
    {
        $this->determineErrors();
        return ($this->errors[$element->getName()] ?? []) === [];
    }

    protected function determineErrors(): void
    {
        $this->form->verifyProtection();
        foreach ($this->form->getElements() as $name => $element) {
            if (!isset($this->verifiedElements[$name])) {
                $element->determineErrors();
                $this->verifiedElements[$name] = true;
            }
        }
    }
}
