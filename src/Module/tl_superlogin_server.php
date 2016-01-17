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
     * remove certificate upload field, if a certificate already exists
     * @param $dc
     */
    public function onCertificateUpload($dc) {

        $authServer = SuperLoginServerModel::findByID(\Input::get('id'));

        if($authServer->server_key != '') {
            unset($GLOBALS['TL_DCA']['tl_superlogin_server']['fields']['server_key']);
        }
    }

    /**
     * helper method
     * @return null
     */
    public function doNotSave()
    {
        return null;
    }

    /**
     * return information about the auth server
     * @param $value
     * @param $dc
     * @return mixed
     */
    public function getAuthServerInfo($value, $dc)
    {
        /*
        if($dc->activeRecord->auth_provider != '')
        {
            $obj = new ClcPlusAuthProvider();
            //$obj = new $dc->activeRecord->auth_provider();
            return $obj->getAuthServerInfo($value, $dc);
        }
        */
        return $value;
    }
}