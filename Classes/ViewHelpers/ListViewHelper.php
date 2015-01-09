<?php

namespace Ameos\AmeosForm\ViewHelpers;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class ListViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Renders lister
     *
     * @param \Ameos\AmeosForm\Library\RecordList $lister the lister
     * @return string html
     */
    public function render($list) {
		return $list->render();
    }
}
