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

$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('\Comolo\SuperLoginClient\ContaoEdition\Module\DisplayAuthProviders', 'addServersToLoginPage');

$GLOBALS['BE_MOD']['superlogin']['superlogin_auth_servers'] = array(
    'tables'       => array('tl_superlogin_server'),
    'icon'         => '/bundles/comolosuperloginclient/img/icon.png',
);