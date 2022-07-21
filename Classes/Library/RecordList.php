<?php

namespace Ameos\AmeosForm\Library;

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
use TYPO3\CMS\Fluid\View\StandaloneView;
use Ameos\AmeosForm\Utility\Events;

class RecordList
{
    /**
     * @var \Ameos\AmeosForm\Form\Search searchform
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
     * @param \Ameos\AmeosForm\Form\AbstractForm $searchform search form
     * @param string $template template path
     * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $context
     */
    public function __construct($searchform, $template, $context, $defaultOrderby = false, $defaultDirection = 'ASC')
    {
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
    protected function initializeView()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setControllerContext($this->context);
        $this->view->setTemplatePathAndFilename($this->template);
    }

    /**
     * initialize sorting
     */
    protected function initializeSorting()
    {
        if (TYPO3_MODE == 'FE') {
            $defaultOrderby   = $GLOBALS['TSFE']->fe_user->getKey('ses', 'form-' . $this->searchform->getIdentifier() . '-orderby');
            $defaultDirection = $GLOBALS['TSFE']->fe_user->getKey('ses', 'form-' . $this->searchform->getIdentifier() . '-direction');
        } else {
            $defaultOrderby = $defaultDirection = null;
        }


        if ($defaultOrderby == null) {
            $defaultOrderby     = $this->defaultOrderby;
        }
        if ($defaultDirection == null) {
            $defaultDirection = $this->defaultDirection;
        }

        $this->orderby = $this->context->getRequest()->hasArgument('sort')
            ? $this->context->getRequest()->getArgument('sort')
            : $defaultOrderby;

        $this->direction = $this->context->getRequest()->hasArgument('direction')
            ? $this->context->getRequest()->getArgument('direction')
            : $defaultDirection;

        // store in session if needed
        if ($this->orderby != $defaultOrderby) {
            $GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->searchform->getIdentifier() . '-orderby', $this->orderby);
        }
        if ($this->direction != $defaultDirection) {
            $GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->searchform->getIdentifier() . '-direction', $this->direction);
        }
    }

    /**
     * assign a value for the template
     * @param string $key key
     * @param string $value the value
     */
    public function assign($key, $value)
    {
        $this->view->assign($key, $value);
    }

    /**
     * return html
     * @return string html
     */
    public function render()
    {
        $this->view->assign('records', $this->searchform->getResults($this->orderby, $this->direction));
        return $this->view->render();
    }
}
