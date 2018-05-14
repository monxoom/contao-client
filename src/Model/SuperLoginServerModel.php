<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   Superlogin
 * @author    Hendrik Obermayer - Comolo GmbH
 * @license   -
 * @copyright 2014-2018 Hendrik Obermayer
 */

namespace Comolo\SuperLoginClient\ContaoEdition\Model;

/**
 * Class SuperLoginServerModel
 * @package Comolo\SuperLoginClient\ContaoEdition\Model
 *
 * @method static findById(int $serverId)
 * @property int $id
 * @property string $secret
 * @property string url_authorize
 * @property string url_access_token
 * @property string url_resource_owner_details
 *
 */
class SuperLoginServerModel extends \Model
{
	protected static $strTable = 'tl_superlogin_server';

    public function getRedirectUrl()
    {
        return \System::getContainer()->get('router')
                ->generate('superlogin_auth_redirect', ['serverId' => $this->id]);
    }
}
