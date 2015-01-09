<?php

namespace Ameos\AmeosForm\Library;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\AmeosForm\Utility\Events;

class RecordList {

	/**
	 * @var \Ameos\AmeosForm\Form\AbstractForm searchform
	 */
	protected $searchform;

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext controller context
	 */
	protected $context;

	/**
	 * @var string template path
	 */
	protected $template;

	/**
	 * @var \TYPO3\CMS\Fluid\View\StandaloneView  the view
	 */
	protected $view;

	/**
	 * @var bool|string order list by this field
	 */
	protected $orderby;

	/**
	 * @var string direction for sorting
	 */
	protected $direction; 
	
	/**
	 * @constructor
	 * @param \Ameos\AmeosForm\Form\AbstractForm $searchform search form
	 * @param string $template template path
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $context
	 */ 
	public function __construct($searchform, $template, $context) {
		$this->searchform = $searchform;
		$this->template = $template;
		$this->context = $context;

		$this->initializeView();
		$this->initializeSorting();
	}

	/**
	 * initialize the view
	 */ 
	protected function initializeView(){
		$this->view = GeneralUtility::makeInstance('\\TYPO3\\CMS\\Fluid\\View\\StandaloneView');	
		$this->view->setControllerContext($this->context);
		$this->view->setTemplatePathAndFilename($this->template);
	}

	/**
	 * initialize sorting
	 */ 
	protected function initializeSorting() {
		$this->orderby = $this->context->getRequest()->hasArgument('sort')
			? $this->context->getRequest()->getArgument('sort')
			: FALSE;
		
		$this->direction = $this->context->getRequest()->hasArgument('direction')
			? $this->context->getRequest()->getArgument('direction')
			: 'ASC';
	}

	/**
	 * assign a value for the template
	 * @param string $key key
	 * @param string $value the value
	 */ 
	public function assign($key, $value) {
		$this->view->assign($key, $value);
	}

	/**
	 * return html
	 * @return string html
	 */ 
	public function render() {
		$this->view->assign('records', $this->searchform->getResults($this->orderby, $this->direction));	
		return $this->view->render();
	}
}
