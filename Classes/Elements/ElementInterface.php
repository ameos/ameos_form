<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Constraints\ConstraintInterface;
use Ameos\AmeosForm\Form\Form;

interface ElementInterface
{
    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form);

    /**
     * form to html
     *
     * @return string
     */
    public function toHtml(): string;

    /**
     * return true if the element is valide
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * add configuration
     *
     * @param string $key configuration key
     * @param mixed $value value
     * @return self
     */
    public function addConfiguration(string $key, mixed $value): self;

    /**
     * return search field
     *
     * @return string
     */
    public function getSearchField(): string;

    /**
     * return where clause
     *
     * @return array|false
     */
    public function getClause(): array|false;

    /**
     * set the value
     *
     * @param mixed $value value
     * @return self
     */
    public function setValue(mixed $value): self;

    /**
     * return the value
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * return the name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * return the html id
     *
     * @return string
     */
    public function getHtmlId(): string;

    /**
     * return errors
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array;

    /**
     * add configuration
     *
     * alias addConfiguration
     * @param string $key configuration key
     * @param mixed $value value
     * @return self
     */
    public function with(string $key, mixed $value): self;

    /**
     * add constraint
     *
     * @param ConstraintInterface $constraint
     * @return self
     */
    public function addConstraint(ConstraintInterface $constraint): self;

    /**
     * determine errors
     *
     * @return self
     */
    public function determineErrors(): self;
}
