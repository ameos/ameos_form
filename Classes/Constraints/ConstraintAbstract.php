<?php

namespace Ameos\AmeosForm\Constraints;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

abstract class ConstraintAbstract implements \Ameos\AmeosForm\Constraints\ConstraintInterface
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
     * @var \Ameos\AmeosForm\Form $form form
     */
    protected $form;

    /**
     * @var \Ameos\AmeosForm\Elements\ElementAbstract $element element
     */
    protected $element;

    /**
     * @constuctor
     *
     * @param   string  $message message
     * @param   \Ameos\AmeosForm\Form $form form
     */
    public function __construct($message, $configuration, $element, $form)
    {
        $this->configuration = $configuration;
        $this->message       = $form->stringUtility->smart($message);
        $this->element       = $element;
        $this->form          = $form;
    }

    /**
     * return the message
     *
     * @return  string the message
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
