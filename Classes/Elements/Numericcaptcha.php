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
use Ameos\AmeosForm\Constraints\Numericcaptcha as NumericcaptchaConstraint;

class Numericcaptcha extends ElementAbstract
{

    /**
     * @constuctor
     *
     * @param   string  $absolutename absolutename
     * @param   string  $name name
     * @param   array   $configuration configuration
     * @param   \Ameos\AmeosForm\Form $form form
     */
    public function __construct($absolutename, $name, $configuration, $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (!$this->form->isSubmitted()) {
            $this->reloadDigit(1);
            $this->reloadDigit(2);
        }
        $errorMessage = isset($configuration['errormessage']) ? $configuration['errormessage'] : 'Numeric captcha is not valid';
        $constraint = GeneralUtility::makeInstance(NumericcaptchaConstraint::class, $errorMessage, [], $this, $form);
        $this->addConstraint($constraint);
    }

    public function reloadDigit($key)
    {
        $sessionKey = 'form-' . $this->form->getIdentifier() . '-' . $this->getHtmlId() . '-digit-' . $key;
        $GLOBALS['TSFE']->fe_user->setKey("ses", $sessionKey, false);
        $this->getDigit($key);
    }

    public function getDigit($key)
    {
        $sessionKey = 'form-' . $this->form->getIdentifier() . '-' . $this->getHtmlId() . '-digit-' . $key;
        if ($GLOBALS["TSFE"]->fe_user->getKey("ses", $sessionKey) == false) {
            $GLOBALS['TSFE']->fe_user->setKey("ses", $sessionKey, rand(1, 9));
        }
        return $GLOBALS["TSFE"]->fe_user->getKey("ses", $sessionKey);
    }
    
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        $sid = md5(uniqid());
        return $this->renderLabel() . $this->renderCaptchaInput();
    }


    /**
     * render captcha picture
     * @return string html
     */
    protected function renderCaptchaInput()
    {
        return '<input type="text" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />';
    }

    protected function renderLabel()
    {
        return '<label for="' . $this->getHtmlId() . '">' . $this->renderOperation() . '</label>';
    }

    protected function renderOperation()
    {
        return $this->getDigit(1) . ' + ' . $this->getDigit(2);
    }
    
    /**
     * return rendering information
     *
     * @return  array rendering information
     */
    public function getRenderingInformation()
    {
        $data = parent::getRenderingInformation();
        $data['input']   = $this->renderCaptchaInput();
        $data['label']   = $this->renderLabel();
        $data['operation']   = $this->renderOperation();
        return $data;
    }
}
