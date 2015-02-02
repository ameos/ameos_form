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
	 * @var bool|string default order list by this field
	 */
	protected $defaultOrderby;

	/**
	 * @var string default direction for sorting
	 */
	protected $defaultDirection; 
	
	/**
	 * @constructor
	 * @param \Ameos\AmeosForm\Form\AbstractForm $searchform search form
	 * @param string $template template path
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $context
	 */ 
	public function __construct($searchform, $template, $context, $defaultOrderby = FALSE, $defaultDirection = 'ASC') {
		$this->searchform = $searchform;
		$this->template = $template;
		$this->context = $context;

		$this->defaultOrderby   = $defaultOrderby;
		$this->defaultDirection = $defaultDirection;

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
		$defaultOrderby   = $GLOBALS['TSFE']->fe_user->getKey('ses', 'form-' . $this->searchform->getIdentifier() . '-orderby');
		$defaultDirection = $GLOBALS['TSFE']->fe_user->getKey('ses', 'form-' . $this->searchform->getIdentifier() . '-direction');

		if($defaultOrderby == NULL) { $defaultOrderby     = $this->defaultOrderby; }
		if($defaultDirection == NULL) { $defaultDirection = $this->defaultDirection; }
		
		$this->orderby = $this->context->getRequest()->hasArgument('sort')
			? $this->context->getRequest()->getArgument('sort')
			: $defaultOrderby;
		
		$this->direction = $this->context->getRequest()->hasArgument('direction')
			? $this->context->getRequest()->getArgument('direction')
			: $defaultDirection;

		// store in session if needed
		if($this->orderby != $defaultOrderby) {
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->searchform->getIdentifier() . '-orderby', $this->orderby);
		}
		if($this->direction != $defaultDirection) {
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->searchform->getIdentifier() . '-direction', $this->direction);
		}
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
