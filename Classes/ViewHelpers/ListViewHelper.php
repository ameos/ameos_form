<?php
namespace Ameos\AmeosForm\ViewHelpers;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class ListViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper 
{

    /**
     * Renders lister
     *
     * @param \Ameos\AmeosForm\Library\RecordList $lister the lister
     * @return string html
     */
    public function render($list) 
    {
		return $list->render();
    }
}
