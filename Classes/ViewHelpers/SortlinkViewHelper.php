<?php

namespace Ameos\AmeosForm\ViewHelpers;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class SortlinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('target', 'string', 'Target of link', FALSE);
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document', FALSE);
	}
	
    /**
     * Renders sort link
     *
     * @param string $column the column
     * @return string html
     */
    public function render($column) {
		$uriBuilder = $this->controllerContext->getUriBuilder();

		$currentDirection = $this->controllerContext->getRequest()->hasArgument('direction')
			? $this->controllerContext->getRequest()->getArgument('direction')
			: 'ASC';

		$currentColumn = $this->controllerContext->getRequest()->hasArgument('sort')
			? $this->controllerContext->getRequest()->getArgument('sort')
			: FALSE;

		$direction = 'ASC';
		if($currentColumn == $column && $currentDirection == 'ASC') {
			$direction = 'DESC';
		}
		
		$uri = $uriBuilder->reset()->uriFor(NULL, ['sort' => $column, 'direction' => $direction]);
		$this->tag->addAttribute('href', $uri);
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);
		return $this->tag->render();
    }
}
