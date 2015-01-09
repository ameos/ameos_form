<?php

namespace Ameos\AmeosForm\Elements;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Captcha extends ElementAbstract {

	/**
	 * @constuctor
	 *
	 * @param	string	$absolutename absolutename
	 * @param	string	$name name
	 * @param	array	$configuration configuration
	 * @param	\Ameos\AmeosForm\Form $form form
	 */
	public function __construct($absolutename, $name, $configuration = [], $form) {
		parent::__construct($absolutename, $name, $configuration, $form);

		$errorMessage = isset($configuration['errormessage']) ? $configuration['errormessage'] : 'Captcha is not valid';
		$validator = GeneralUtility::makeInstance('\\Ameos\\AmeosForm\\Validators\\Captcha', $errorMessage, [], $this, $form);
		$this->validator($validator);
	}
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		$sid = md5(uniqid());
		return $this->renderCaptchaPicture() . $this->renderCaptchaRefresh() . $this->renderCaptchaInput();
	}

	/**
	 * render captcha picture
	 * @return string html
	 */
	protected function renderCaptchaPicture() {
		return '<img id="' . $this->getHtmlId() . '-image" src="/typo3conf/ext/ameos_form/Resources/Public/Captcha/securimage_show.php?sid=' . $sid . '" alt="CAPTCHA Image" />';
	}

	/**
	 * render refresh captcha
	 * @return string html
	 */
	protected function renderCaptchaRefresh() {
		return '<a href="#" title="Refresh Image" onclick="document.getElementById(\'' . $this->getHtmlId() . '-image\').src = \'/typo3conf/ext/ameos_form/Resources/Public/Captcha/securimage_show.php?sid=\' + Math.random(); this.blur(); return false">
				<img src="/typo3conf/ext/ameos_form/Resources/Public/Captcha/images/refresh.png" alt="Reload Image" onclick="this.blur()" />
			</a>';
	}
	
	/**
	 * render captcha picture
	 * @return string html
	 */
	protected function renderCaptchaInput() {
		return '<input type="text" id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '" value="' . $this->getValue() . '"' . $this->getAttributes() . ' />';
	}
	
	/**
	 * return rendering information
	 *
	 * @return	array rendering information
	 */
	public function getRenderingInformation() {
		$data = parent::getRenderingInformation();
		$data['captcha'] = $this->renderCaptchaPicture();
		$data['refresh'] = $this->renderCaptchaRefresh();
		$data['input']   = $this->renderCaptchaInput();
		return $data;
	}
}
