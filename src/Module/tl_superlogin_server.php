<?php
/**
 * @package   AuthClient
 * @author    Hendrik Obermayer - Comolo GmbH
 * @license   -
 * @copyright 2015 Hendrik Obermayer
 */

namespace Comolo\SuperLoginClient\ContaoEdition\Module;

use Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel;

/**
 * Class tl_superlogin_server
 * @package AuthClient
 * @deprecated
 */
class tl_superlogin_server
{
    /**
     * Run authprovider method when the form gets submitted; triggered by the onsubmit event
     * @param $dc
     */
    public function onSubmitDca($dc) {
        //$obj = new ClcPlusAuthProvider();
        //$obj = new "\Comolo\SuperLoginClient\ContaoEdition\Module\\" . $dc->activeRecord->auth_provider();
        //$obj->onSubmitDcForm($dc);
    }

    /**
     * helper method
     * @return null
     */
    public function doNotSave()
    {
        return null;
    }
}
