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

/**
 * Class SuperLoginServerModel
 * @package Comolo\SuperLoginClient\ContaoEdition\Model
 * @method static findById(int $serverId)
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

/*
 * Fix autoload bug
 */
# TODO: remove
#class_alias('Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel', '\SuperLoginServerModel');
