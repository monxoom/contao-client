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

namespace Comolo\SuperLoginClient\ContaoEdition\BackendModule;

use Comolo\SuperLoginClient\ContaoEdition\Model\SuperLoginServerModel;
use Environment;

/**
 * Class LoginAuth
 *
 * @copyright  2014-2018 Hendrik Obermayer
 * @author     Hendrik Obermayer - Comolo GmbH
 */
class DisplayAuthProviders extends \System
{
    /**
     * Display option field in backend login
     *
     * @param $strContent
     * @param $strTemplate
     * @return mixed
     */
	public function addServersToLoginPage($strContent, $strTemplate)
    {
        if ($strTemplate == 'be_login')
        {
            $template = new \BackendTemplate('mod_superlogin_loginpage');
            $template->loginServers = SuperLoginServerModel::findAll();

            $searchString = '<div class="tl_info" id="javascript">';
            $strContent = str_replace($searchString, $template->parse().$searchString, $strContent);

            $searchString = '</head>';
            $cssLink = '<link rel="stylesheet" href="'.Environment::get('path').'/bundles/comolosuperloginclient/css/superlogin.css">';
            $strContent = str_replace($searchString, $cssLink.$searchString, $strContent);
        }

        return $strContent;
    }
}
