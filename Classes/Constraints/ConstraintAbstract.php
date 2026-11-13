<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Elements\ElementInterface;
use Ameos\AmeosForm\Form\Form;

abstract class ConstraintAbstract implements ConstraintInterface
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(
        protected string $message,
        protected array $configuration,
        protected ElementInterface $element,
        protected Form $form,
    ) {}

    public function getMessage(): ?string
    {
        return $this->message;
    }

    abstract public function isValid(mixed $value): bool;

    protected function stringify(mixed $value): string
    {
        return is_scalar($value) || $value instanceof \Stringable ? (string) $value : '';
    }
}
