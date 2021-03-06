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
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Ameos\AmeosForm\Utility\FormUtility;
use Ameos\AmeosForm\Utility\Events;
use Ameos\AmeosForm\Utility\StringUtility as AmeosStringUtility;
use Ameos\AmeosForm\Utility\ErrorManager;

abstract class AbstractForm
{
    
    /**
     * @var TYPO3\CMS\Extbase\Object\ObjectManager $objectManager
     */
    protected $objectManager;
    
    /**
     * @var Ameos\AmeosForm\Utility\ErrorManager $errorManager error manager
     */
    protected $errorManager;
    
    /**
     * @var string $identifer identifier
     */
    protected $identifier;

    /**
     * @var string $extensionName extension name
     */
    protected $extensionName;

    /**
     * @var array $elements elements
     */
    protected $elements;

    /**
     * @var string $mode mode
     */
    protected $mode;

    /**
     * @var bool $enableCrsftoken enable crsf token
     */
    protected $enableCsrftoken = true;

    /**
     * @var bool $enableHoneypot enable honey pot
     */
    protected $enableHoneypot = true;
    
    /**
     * @var Ameos\AmeosForm\Utility\StringUtility $stringUtility
     */
    public $stringUtility;
    
    /**
     * @constuctor
     *
     * @param   string $identifier form identifier
     */
    public function __construct($identifier)
    {
        $this->elements   = [];
        $this->identifier = $identifier;
        $this->enableCsrftoken = TYPO3_MODE == 'FE' ? true : false;

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        
        $this->stringUtility = $this->objectManager->get(AmeosStringUtility::class, $this);
        $this->errorManager  = $this->objectManager->get(ErrorManager::class, $this);
    }
    
    /**
     * return error manager instance
     * @return Ameos\AmeosForm\Utility\ErrorManager
     */
    public function getErrorManager()
    {
        return $this->errorManager;
    }
    
    /**
     * return identifier
     * @return string identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * enable csrf token
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function enableCsrftoken()
    {
        $this->enableCsrftoken = true;
        return $this;
    }

    /**
     * disable csrf token
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function disableCsrftoken()
    {
        $this->enableCsrftoken = false;
        return $this;
    }

    /**
     * return TRUE if csrf token is enabled
     * @return bool
     */
    public function csrftokenIsEnabled()
    {
        return $this->enableCsrftoken;
    }

    /**
     * enable honeypot
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function enableHoneypot()
    {
        $this->enableHoneypot = true;
        return $this;
    }

    /**
     * disable honeypot
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function disableHoneypot()
    {
        $this->enableHoneypot = false;
        return $this;
    }

    /**
     * return TRUE if honeypot is enabled
     * @return bool
     */
    public function honeypotIsEnabled()
    {
        return $this->enableHoneypot;
    }

    /**
     * enable flash message
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function enableFlashMessage()
    {
        $this->errorManager->enableFlashMessage();
        return $this;
    }

    /**
     * disable flash message
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function disableFlashMessage()
    {
        $this->errorManager->disableFlashMessage();
        return $this;
    }

    /**
     * return TRUE if flash message is enabled
     * @return bool
     */
    public function flashMessageIsEnabled()
    {
        return $this->errorManager->flashMessageIsEnabled();
    }

    /**
     * set extension name
     * @param string $extensionName extension name
     * @return \Ameos\AmeosForm\Form\AbstractForm
     */
    public function setExtensionName($extensionName)
    {
        $this->extensionName = $extensionName;
        return $this;
    }
    
    /**
     * return extension name
     * @return string extension name
     */
    public function getExtensionName()
    {
        return $this->extensionName;
    }
    
    /**
     * return elements
     * @return array elements
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * return element
     * @param   string $name element name
     * @return  \Ameos\AmeosForm\Elements\ElementInterface
     */
    public function getElement($name)
    {
        return $this->hasElement($name) ? $this->elements[$name] : false;
    }

    /**
     * return TRUE if element exist
     * @param   string $name element name
     * @return  bool
     */
    public function hasElement($name)
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * return element
     * alias getElement
     * @param   string $name element name
     * @return  \Ameos\AmeosForm\Elements\ElementInterface
     */
    public function get($name)
    {
        return $this->getElement($name);
    }

    /**
     * return TRUE if element exist
     * alias hasElement
     * @param   string $name element name
     * @return  bool
     */
    public function has($name)
    {
        return $this->hasElement($name);
    }

    /**
     * add element fo the form
     *
     * @param   string  $type element type
     * @param   string  $name element name
     * @param   string  $configuration element configuration
     * @return  \Ameos\AmeosForm\Form this
     */
    public function add($name, $type = '', $configuration = [])
    {
        $absolutename = $this->identifier . '[' . $name . ']';
        $element = FormUtility::makeElementInstance($absolutename, $name, $type, $configuration, $this);

        if ($this->getMode() == 'search/extbase' || $this->getMode() == 'search/classic') {
            if (array_key_exists($name, $this->clauses)) {
                $element->setValue($this->clauses[$name]['elementvalue']);
            }
        }

        $this->elements[$name] = $element;
        return $this;
    }
    
    /**
     * Return mode
     *
     * @return  string mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        if (!$this->isSubmitted()) {
            $csrftoken = GeneralUtility::shortMD5(time() . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
            $GLOBALS['TSFE']->fe_user->setKey('ses', $this->getIdentifier() . '-csrftoken', $csrftoken);
            $GLOBALS['TSFE']->fe_user->storeSessionData();
        } else {
            $csrftoken = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->getIdentifier() . '-csrftoken');
        }
        
        $html = '<form method="post">';
        foreach ($this->elements as $element) {
            $html .= $element->toHtml();
        }
        $html .= '<input type="hidden" id="' . $this->getIdentifier() . '-issubmitted" value="1" name="' . $this->getIdentifier() . '[issubmitted]" />';
        if ($this->csrftokenIsEnabled()) {
            $html .= '<input type="hidden" id="' . $this->getIdentifier() . '-csrftoken" value="' . $csrftoken . '" name="' . $this->getIdentifier() . '[csrftoken]" />';
        }
        if ($this->honeypotIsEnabled()) {
            $html .= '<input type="text" id="' . $this->getIdentifier() . '-winnie" value="" name="' . $this->getIdentifier() . '[winnie]" />';
        }
        $html .= '</form>';
        return $html;
    }

    /**
     * bind request to the form
     * @param   $request \TYPO3\CMS\Extbase\Mvc\Request|array the request
     * @return  \Ameos\AmeosForm\Form this
     */
    public function bindRequest($request)
    {
        if (!is_array($request) && !is_a($request, 'TYPO3\\CMS\\Extbase\\Mvc\\Request')) {
            throw new \Exception('request must be an array or an extbase request (TYPO3\\CMS\\Extbase\\Mvc\\Request)');
        }

        $requestDatas = is_a($request, 'TYPO3\\CMS\\Extbase\\Mvc\\Request') ? $request->getArguments() : $request;
        
        if ($this->csrftokenIsEnabled()) {
            if ($requestDatas['csrftoken'] == '' || $requestDatas['csrftoken'] != $GLOBALS['TSFE']->fe_user->getKey('ses', $this->getIdentifier() . '-csrftoken')) {
                throw new \Exception('Forbidden: invalid csrf token');
            }
        }
        if ($this->honeypotIsEnabled()) {
            if (isset($requestDatas['winnie']) && $requestDatas['winnie'] != '') {
                throw new \Exception('Forbidden: you are a bot');
            }
        }

        foreach ($this->elements as $elementName => $element) {
            if (array_key_exists($elementName, $requestDatas)) {
                $element->setValue($requestDatas[$elementName]);
            } else {
                $element->setValue('');
            }
        }
        
        return $this;
    }

    /**
     * is submitted
     * @return bool
     */
    public function isSubmitted()
    {
        $post = GeneralUtility::_POST($this->identifier);
        return isset($post['issubmitted']) && $post['issubmitted'] == 1;
    }
    
    /**
     * return submitter
     * @return Ameos\AmeosForm\Elements\Submit|FALSE
     */
    public function getSubmitter()
    {
        if ($this->isSubmitted()) {
            foreach ($this->getElements() as $element) {
                if (is_a($element, 'Ameos\\AmeosForm\\Elements\\Submit') && $element->isClicked()) {
                    return $element;
                }
            }
        }
        
        return false;
    }
    
    /**
     * call magic method
     * return element if exist
     * @param string $method method
     * @param array $parameters parameters
     * @return Ameos\AmeosForm\Elements\AbstractElement|FALSE
     */
    public function __call($method, $parameters)
    {
        if (StringUtility::beginsWith($method, 'get')) {
            $elementName = substr($method, 3);
            if ($this->has($elementName)) {
                return $this->get($elementName);
            }
            
            $elementName = lcfirst($elementName);
            if ($this->has($elementName)) {
                return $this->get($elementName);
            }
            
            $elementName = GeneralUtility::camelCaseToLowerCaseUnderscored($elementName);
            if ($this->has($elementName)) {
                return $this->get($elementName);
            }
            
            $elementName = str_replace('_', '-', $elementName);
            if ($this->has($elementName)) {
                return $this->get($elementName);
            }
        }
        
        if (StringUtility::beginsWith($method, 'with')) {
            $elementName = substr($method, 4);
            if ($this->has($elementName)) {
                return $this->get($elementName)->with($parameters[0], $parameters[1]);
            }
            
            $elementName = lcfirst($elementName);
            if ($this->has($elementName)) {
                return $this->get($elementName)->with($parameters[0], $parameters[1]);
            }
            
            $elementName = GeneralUtility::camelCaseToLowerCaseUnderscored($elementName);
            if ($this->has($elementName)) {
                return $this->get($elementName)->with($parameters[0], $parameters[1]);
            }
            
            $elementName = str_replace('_', '-', $elementName);
            if ($this->has($elementName)) {
                return $this->get($elementName)->with($parameters[0], $parameters[1]);
            }
        }
        
        return false;
    }
}
