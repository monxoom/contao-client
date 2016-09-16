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

namespace Comolo\SuperLoginClient\ContaoEdition\Model;

use  Comolo\SuperLoginClient\ContaoEdition\AuthProvider\OAuth2Provider;

class SuperLoginServerModel extends \Model
{
	protected static $strTable = 'tl_superlogin_server';

    public function getRedirectUrl()
    {
        return \System::getContainer()->get('router')
                ->generate('superlogin_auth_redirect', array('serverId' => $this->id));
    }
}

/*
 * Fix autoload bug
 */
class_alias('Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel', '\SuperLoginServerModel');
