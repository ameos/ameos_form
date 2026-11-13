<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Constraints\ConstraintInterface;
use Ameos\AmeosForm\Form\Form;

interface ElementInterface
{
    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form);

    public function toHtml(): string;

    public function isValid(): bool;

    public function addConfiguration(string $key, mixed $value): self;

    public function getSearchField(): string;

    /**
     * @return array<string, mixed>|false where clause, false if the element does not filter the search
     */
    public function getClause(): array|false;

    public function isSearchable(): bool;

    public function setValue(mixed $value): self;

    public function getValue(): mixed;

    public function getName(): string;

    public function getHtmlId(): string;

    public function getAbsoluteName(): string;

    /**
     * @return list<string>
     */
    public function getErrors(): array;

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array;

    public function getConfigurationItem(string $key): mixed;

    /**
     * @return array<string, mixed>
     */
    public function getRenderingInformation(): array;

    /**
     * Alias of addConfiguration()
     */
    public function with(string $key, mixed $value): self;

    public function addConstraint(ConstraintInterface $constraint): self;

    public function determineErrors(): self;

    public function getDatalist(): string;

    public function getAttributes(): string;

    /**
     * @param string $type "text" or "bool"
     */
    public function getAttribute(string $key, string $type = 'text'): string;

    public function getClassAttribute(): string;

    public function getCustomAttribute(): string;
}
