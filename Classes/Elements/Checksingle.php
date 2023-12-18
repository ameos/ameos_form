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

class Checksingle extends ElementAbstract
{
    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        $checked = ($this->getValue() == 1) ? ' checked="checked"' : '';
        return
            '<input type="checkbox"'
            . ' id="' . $this->getHtmlId() . '"'
            . ' name="' . $this->absolutename . '"'
            . ' value="1"' . $this->getAttributes() . $checked . ' />';
    }
}
