<?php

/*
 * phpWebFileManager - simple file management PHP script
 *
 * init.inc.php - script initialization
 *                (standalone or PostNuke module)
 * ____________________________________________________________
 *
 * Developed by Ondrej Jombik <nepto@platon.sk>
 * Copyright (c) 2001-2005 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpWebFileManager/
 */

/* $Platon: phpWebFileManager/init.inc.php,v 1.14 2005/10/01 17:17:34 nepto Exp $ */

/*
 * Basic PostNuke recognition and settings
 */

/* Strange PostNuke rules needs to have this $ModName not as normal people
   will called it $PN_ModName. */
$ModName       = null;
$PN_PathPrefix = '';

if (defined('LOADED_AS_MODULE')) {
    $ModName = basename(dirname(__FILE__));
    $PN_PathPrefix = "modules/$ModName/";
} else {
    $PN_PathPrefix  = dirname(__FILE__);
    $PN_PathPrefix .= '/';
}

/*
 * Config file inclusion
 */

if (! @file_exists($PN_PathPrefix . 'config.inc.php')) {
    exit;
}

require_once $PN_PathPrefix . 'config.inc.php';

/*
 * Local config file inclusion
 */

if (@file_exists($PN_PathPrefix . 'config-local.inc.php')) {
    require_once $PN_PathPrefix . 'config-local.inc.php';
}

/*
 * Icons configuration file inclusion
 */

if ($fm_cfg['show']['icons']) {
    include_once $PN_PathPrefix . 'icons.inc.php';
}

/*
 * Other initialization (language settings, ...)
 */

if (defined('LOADED_AS_MODULE')) {

    /*
     * PostNuke use original variables feature for correct module calls.
     */

    if (strlen($fm_cfg['origvars']) > 0)
        $fm_cfg['origvars'] .= '&';

    $fm_cfg['origvars'] .= "op=modload&name=$ModName&file=index";

    /*
     * Icons URL
     */

    if ($fm_cfg['url']['icons'][0] != '/') {
        $fm_cfg['url']['icons'] = "modules/$ModName/".$fm_cfg['url']['icons'];
    }

    /*
     * Assign theme settings from PostNuke theme.
     */

    $fm_cfg['color']['even'] = $bgcolor2;
    $fm_cfg['color']['odd']  = $bgcolor1;

    include 'header.php';
}


/*
 * Language file loading
 */

if (defined('LOADED_AS_MODULE') && function_exists('modules_get_language')) {
    modules_get_language();
} else {
    if (file_exists($PN_PathPrefix.'lang/'.$fm_cfg['lang'].'/global.php')) {
        include $PN_PathPrefix.'lang/'.$fm_cfg['lang'].'/global.php';
    } else {
        include $PN_PathPrefix.'lang/eng/global.php';
    } 
}

?>
