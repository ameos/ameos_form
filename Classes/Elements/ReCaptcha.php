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

class ReCaptcha extends ElementAbstract 
{
	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) 
	{
		parent::__construct($absolutename, $name, $configuration, $form);

        $GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile('https://www.google.com/recaptcha/api.js');

		$errorMessage = isset($configuration['errormessage']) ? $configuration['errormessage'] : 'ReCaptcha is not valid';
		$constraint = GeneralUtility::makeInstance(
            'Ameos\\AmeosForm\\Constraints\\ReCaptcha',
            $errorMessage,
            ['privateKey' => $configuration['privateKey']],
            $this,
            $form
        );
		$this->addConstraint($constraint);
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() 
	{
        return '<div class="g-recaptcha" data-sitekey="' . $this->configuration['publicKey'] . '"></div>';
	}
}
