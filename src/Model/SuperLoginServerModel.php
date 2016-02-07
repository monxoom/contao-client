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
namespace Comolo\SuperLoginClient\ContaoEdition\Model;
use  Comolo\SuperLoginClient\ContaoEdition\AuthProvider\OAuth2Provider;

/**
 * Class AuthServerModel
 *
 * @copyright  2014 Hendrik Obermayer
 * @author     Hendrik Obermayer - Comolo GmbH
 * @package    Devtools
 */
class SuperLoginServerModel extends \Model
{

	/**
	 * Name of the table
	 * @var string
	 */
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