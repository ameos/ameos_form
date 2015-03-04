<?php

namespace Ameos\AmeosForm\Utility;

class UserUtility {

    /**
     * return true if the user is logged
     *
     * @return	bool true if the user is logged
     */
    public static function isLogged() {
        return isset($GLOBALS['TSFE']->fe_user->user['uid']) && $GLOBALS['TSFE']->fe_user->user['uid'] > 0;
    }
}
