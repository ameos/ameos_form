<?php
namespace Ameos\AmeosForm\Elements;

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

class Password extends ElementAbstract 
{
    
    /**
     * @var bool $searchable searchable
     */
    protected $searchable = false;
    
    /**
     * @constuctor
     *
     * @param    string    $absolutename absolutename
     * @param    string    $name name
     * @param    array    $configuration configuration
     * @param    \Ameos\AmeosForm\Form $form form
     */
    public function __construct($absolutename, $name, $configuration = [], $form) 
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        $this->configuration['encrypt'] = isset($configuration['encrypt']) ? (bool)$configuration['encrypt'] : true;
        $this->configuration['fill_value'] = isset($configuration['fill_value']) ? (bool)$configuration['fill_value'] : false;
        $this->configuration['fill_value_iferror'] = isset($configuration['fill_value']) ? (bool)$configuration['fill_value_iferror'] : true;
    }
    
    /**
     * form to html
     *
     * @return    string the html
     */
    public function toHtml()
    {
        $attributes = $this->getAttributes();
        if ($this->configuration['fill_value']
          || ($this->form->isSubmitted() && !$this->form->isValid() && $this->configuration['fill_value_iferror'])) {
            $attributes.= ' value="' . $this->getValue() . '"';
        }
        return '<input type="password" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' . $attributes . ' />';
    }

    /**
     * set the value
     * 
     * @param    string    $value value
     * @return     \Ameos\AmeosForm\Elements\ElementAbstract this
     */
    public function setValue($value) 
    {        
        if ($this->configuration['encrypt']) {
            Events::getInstance($this->form->getIdentifier())->registerEvent('form_is_valid', [$this, 'encryptPassword'], [
                'password' => $value,
            ]);
        }
        
        $this->valueSetted = true;
        $this->value = $value;

        if ($this->form !== false) {
            if ($this->form->getMode() == 'crud/extbase' && $value != '') {
                $method = 'set' . \Ameos\AmeosForm\Utility\StringUtility::camelCase($this->name);
                if (method_exists($this->form->getModel(), $method)) {
                    $this->form->getModel()->$method($value);
                }
            }

            if ($this->form->getMode() == 'crud/classic' && $value != '') {
                if ($this->form->hasData($this->name)) {
                    $this->form->setData($this->name, $value);
                }
            }
        }

        return $this;
    }

    /**
     * encrypt password
     * @param string $password password
     */
    public function encryptPassword($password)
    {
        if (version_compare(TYPO3_version, '9', '>=')) {
            $hashInstance = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory::class)
                ->getDefaultHashInstance('FE');
            $this->setValue($hashInstance->getHashedPassword($password));
        } elseif (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled() && $password != '') {
            $password = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance(null)
                ->getHashedPassword($password);
            $this->setValue($password);
        }
    }
}
