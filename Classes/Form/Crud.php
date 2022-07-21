<?php

namespace Ameos\AmeosForm\Form;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\Events;
use Ameos\AmeosForm\Constraints;

class Crud extends \Ameos\AmeosForm\Form\AbstractForm
{
    /**
     * @var bool $elementsConstraintsAreChecked true if elements constraints are checked
     */
    protected $elementsConstraintsAreChecked = false;

    /**
     * @var array $errorsByElement errorsByElement
     */
    protected $errorsByElement = [];

    /**
     * @constuctor
     *
     * @param   string $identifier form identifier
     * @param   \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model model
     */
    public function __construct($identifier)
    {
        parent::__construct($identifier);
        $this->mode = 'crud/manual';
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
            switch ($type) {
                case 'email':
                    $constraint = GeneralUtility::makeInstance(Constraints\Email::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'sameas':
                    $constraint = GeneralUtility::makeInstance(Constraints\Sameas::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'unique':
                    $constraint = GeneralUtility::makeInstance(Constraints\Unique::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'fileextension':
                    $constraint = GeneralUtility::makeInstance(Constraints\Fileextension::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'filesize':
                    $constraint = GeneralUtility::makeInstance(Constraints\Filesize::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'captcha':
                    $constraint = GeneralUtility::makeInstance(Constraints\Captcha::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'custom':
                    $constraint = GeneralUtility::makeInstance(Constraints\Custom::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
                case 'required':      // no-break
                default:
                    $constraint = GeneralUtility::makeInstance(Constraints\Required::class, $message, $configuration, $this->getElement($elementName), $this);
                    break;
            }

            $this->get($elementName)->addConstraint($constraint);
        }

        return $this;
    }

    /**
     * return true if the form is valide
     *
     * @return  bool true if is a valid form
     */
    public function isValid()
    {
        if ($this->errorManager->isValid()) {
            Events::getInstance($this->getIdentifier())->trigger('form_is_valid');
        }

        return $this->errorManager->isValid();
    }

    /**
     * Return errors
     *
     * @return  array errors
     */
    public function getErrors()
    {
        return $this->errorManager->getAllErrorsMerged();
    }

    /**
     * Return errors by elements
     *
     * @return  array errors
     */
    public function getErrorsByElement()
    {
        return $this->errorManager->getAllErrors();
    }

    /**
     * Return errors by elements
     *
     * @return  array errors
     */
    public function getErrorsFormElement($elementName)
    {
        return $this->errorManager->getErrors($elementName);
    }
}
