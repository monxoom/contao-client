<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   AuthClient
 * @author    Hendrik Obermayer - Comolo GmbH
 * @license   -
 * @copyright 2014 Hendrik Obermayer
 */


/**
 * Namespace
 */
namespace Comolo\SuperLoginClient\ContaoEdition\AuthProvider;


/**
 * Class AuthProvider
 *
 * @copyright  2014 Hendrik Obermayer
 * @author     Hendrik Obermayer - Comolo GmbH
 * @package    Devtools
 */
abstract class AuthProvider extends \System
{
    protected $server_address;
    protected $public_id;
    protected $private_key;
    protected $serverId;
    protected $server_key;


    public function __construct() {

    }

    public function setServerId($serverId) {
        $this->serverId = $serverId;
    }

    public function setServerAddress($server_address) {
        $this->server_address = $server_address;
    }

    public function setPublicId($public_id) {
        $this->public_id = $public_id;
    }

    public function run() {
        $this->runRequest();
    }

    public function getReturnUrl($serverId) {
        
        $returnUrl = \System::getContainer()->get('router')->generate('superlogin_auth', ['serverId' => $serverId], true);
        
        //$returnUrl = $this->Environment->url . $this->Environment->requestUri;
        //$delimiter = (\Input::get('referer')) ? '&' : '?';
        
        //$returnUrl = $baseUrl . 
        
        return $returnUrl;
    }

    public function onSubmitDcForm($dc) {
        return;
    }
}
