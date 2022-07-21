<?php

namespace Ameos\AmeosForm\Utility;

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

class UserUtility
{
    /**
     * return true if the user is logged
     *
     * @return  bool true if the user is logged
     */
    public static function isLogged()
    {
        return isset($GLOBALS['TSFE']->fe_user->user['uid']) && $GLOBALS['TSFE']->fe_user->user['uid'] > 0;
    }
}
