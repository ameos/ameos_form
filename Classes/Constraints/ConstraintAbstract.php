<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Elements\ElementAbstract;

abstract class ConstraintAbstract implements ConstraintInterface
{
    /**
     * @var string $message message
     */
    protected $message;

    /**
     * @var array $configuration configuration
     */
    protected $configuration;

    /**
     * @var \Ameos\AmeosForm\Form\Form $form form
     */
    protected $form;

    /**
     * @var ElementAbstract $element element
     */
    protected $element;

    /**
     * @constuctor
     *
     * @param   string  $message message
     * @param   \Ameos\AmeosForm\Form\Form $form form
     */
    public function __construct($message, $configuration, $element, $form)
    {
        $this->configuration = $configuration;
        $this->message       = $message;
        $this->element       = $element;
        $this->form          = $form;
    }

    /**
     * return the message
     *
     * @return ?string the message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * return true if the element is valide
     *
     * @param   string $value value to test
     * @return  bool true if the element is valide
     */
    abstract public function isValid($value);
}
