<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   AuthClient
 * @author    Hendrik Obermayer - Comolo GmbH
 * @license   -
 * @copyright 2014-2015 Hendrik Obermayer
 */

$GLOBALS['TL_HOOKS']['checkCredentials'][] = array('\Comolo\SuperLoginClient\ContaoEdition\Module\LoginAuth', 'loginUserHookPassword');
$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('\Comolo\SuperLoginClient\ContaoEdition\Module\LoginAuth', 'listenForAuthResponse');
$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('\Comolo\SuperLoginClient\ContaoEdition\Module\LoginAuth', 'listenForAuthRequest');
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('\Comolo\SuperLoginClient\ContaoEdition\Module\LoginAuth', 'addServersToLoginPage');

$GLOBALS['BE_MOD']['superlogin']['superlogin_auth_servers'] = array(
    'tables'       => array('tl_superlogin_server'),
    'icon'         => '/bundles/comolosuperloginclient/img/icon.png',
);