<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   AuthClient
 * @author    Hendrik Obermayer - Comolo GmbH
 * @license   -
 * @copyright 2014-2018 Hendrik Obermayer
 */

if (TL_MODE == 'BE') {
	$GLOBALS['TL_CSS'][] = Environment::get('path').'/bundles/comolosuperloginclient/css/backend.css';
}

$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = [
    \Comolo\SuperLoginClient\ContaoEdition\BackendModule\DisplayAuthProviders::class, 'addServersToLoginPage'
];

$GLOBALS['BE_MOD']['superlogin']['superlogin_auth_servers'] = [
    'tables'       => ['tl_superlogin_server'],
];


$GLOBALS['TL_MODELS']['tl_superlogin_server'] = \Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel::class;
