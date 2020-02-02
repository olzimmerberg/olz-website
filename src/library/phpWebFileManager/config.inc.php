<?php

/*
 * phpWebFileManager - simple file management PHP script
 *
 * config.inc.php - configuration file
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

/* $Platon: phpWebFileManager/config.inc.php,v 1.30 2005/10/01 17:17:34 nepto Exp $ */

/*
 * User defined preprocess
 *
 * If you want to secure this script with some include("auth.php") or
 * something like that, that stuff goes here. phpWebFileManager
 * includes this config file before doing anything else.
 */

/* Example:
 *
 *   $orig_dir = getcwd();
 *   chdir(some_dir);
 *   require_once 'auth.php';
 *   chdir($orig_dir);
 *
 * or simply use phpWebFileManager auth plugin
 *
 *   require_once 'plugins/auth.php';
 */

/* 
 * Original variables 
 *
 * Keep this CGI variables while proccessing phpWebFileManager. 
 * If there are more than one variable, separate it with '&'.
 * Example: action=show_files&user=12&key=1234567890abcdef
 */

$fm_cfg['origvars'] = '';

/*
 * Language
 *
 * Used language. Language files are in the lang/ directory. Enter
 * particular subdirectory name without ending slash in this option.
 * If your language is not included there, please translate base
 * lang/eng/global.php file and submit it on the phpWebFileManager
 * project page. Than it can be included in the future releases.
 */

$fm_cfg['lang'] = 'ger';

/*
 * Directories constants
 * 
 * First constant defines root directory. phpWebFileManager guarantee
 * that is impossible to get to the higher level. This parameter could
 * be only relative to the phpWebFileManager script on the same web
 * server. Support for absolute path will be added nearly in future.
 *
 * Second constant defines starting directory. It says where to start
 * when it is runned first time of session. Remember that starting
 * directory is relative to root directory.
 */
/*if ($root == '') {
	if ($local)
		{$fm_cfg['dir']['root'] = "";} // = http://localhost:8888/olzimmerberg.ch/
	elseif (in_array('all',split(' ',$_SESSION['auth'])))*/
	
	/*if (in_array('all',split(' ',$_SESSION['auth'])))
		{$fm_cfg['dir']['root'] = "";} // = http://localhost:8888/olzimmerberg.ch/
	else
		{$fm_cfg['dir']['root'] = 'OLZimmerbergAblage';} // = http://olzimmerberg.ch/html/OLZimmerbergAblage
	$fm_cfg['dir']['root'] = "OLZimmerbergAblage";
	}
else
	{$fm_cfg['dir']['root'] = $root;
	}*/
//$fm_cfg['dir']['root']  = '';
$fm_cfg['dir']['root'] = (in_array('all',explode(' ',$_SESSION['auth']))) ? '' : "OLZimmerbergAblage";
$fm_cfg['dir']['start'] = 'OLZimmerbergAblage'; // = http://olzimmerberg.ch/html/OLZimmerbergAblage

/*
 * CGI variables prefix
 *
 * Here is possible to define prefix of all CGI variables used by
 * phpWebFileManager.
 */
$fm_cfg['cgi'] = 'fm_';

/*
 * Links prefix
 * 
 * First option specifies prefix component used to build links
 * (A HREF references) for View feature.
 * 
 * Second option will be used for building image references
 * (<IMG SRC ...>). On most from the systems you can use /icns/
 * to use default Apache icons. By default is icns/ used, what
 * means to use icons from phpWebFileManager distribution.
 *
 * Both of these options are not rawurlencode()-ed.
 */

// Uncomment this to enable file.php plugin
// $fm_cfg['url']['root'] = 'plugins/file.php?'.$fm_cfg['cgi'].'path=';
$fm_cfg['url']['root'] = '';

// This should also works on Apache webservers
// $fm_cfg['url']['icons'] = '/icns/small/';
$fm_cfg['url']['icons'] = 'icns/';

/*
 * File create & upload constants
 *
 * First constant specifies creation mode of new directories. It must
 * be set. If unsure, leave default value of 0777.
 * 
 * Second constant specifies creation mode of new files or mode for
 * uploaded files. Set it to 0 if you want to create files with
 * default premissions.
 */

$fm_cfg['mode']['dir']  = 0777;
$fm_cfg['mode']['file'] = 0;

/*
 * Date format
 *
 * Every language has its own date format string. You can override it
 * here by specifing new date format. It will be in exact form passed
 * to date() function. Leave this variable empty, if you want to use
 * language specific date format. 
 *
 * Examples:
 *   'M&\n\b\s\p;d,&\n\b\s\p;Y'  // Mar 24, 2002
 *   'd.&\n\b\s\pm.&\n\b\s\pY'   // 25. 03. 2002
 *   'd.m.Y'                     // 25.03.2002
 */

$fm_cfg['format']['date'] = '';

/*
 * Column show constants
 *
 * Allow/disable displaying of specific column.
 */

$fm_cfg['show']['icons'] = 1;
$fm_cfg['show']['size']  = 1;
$fm_cfg['show']['date']  = 1;

/*
 * Features enable constants
 *
 * You can enable some features provided by phpWebFileManager by
 * setting up appropriate constant to value of 1. If you want to deny
 * all features, set all constants to value of 0.
 *
 * Following directory symlinks is forbidden by default.
 */

$fm_cfg['perm']['dir']['rename']  = 1;
$fm_cfg['perm']['dir']['remove']  = 1;
$fm_cfg['perm']['dir']['create']  = 1;
$fm_cfg['perm']['dir']['enter']   = 1;
$fm_cfg['perm']['dir']['follow_symlinks'] = 0;

$fm_cfg['perm']['file']['rename'] = 1;
$fm_cfg['perm']['file']['delete'] = 1;
$fm_cfg['perm']['file']['create'] = 1;
$fm_cfg['perm']['file']['upload'] = 1; // increase for more files
$fm_cfg['perm']['file']['view']   = 1;
$fm_cfg['perm']['file']['edit']   = 0;
$fm_cfg['perm']['file']['save']   = 1;

/*
 * External resources constants
 *
 * External resources are files present on system, which can phpWebFileManager
 * use to extend its features. Array of possible locations must be specified.
 * First readable file from list will be used. If any from locations does not
 * match on your system, just push you one in the top of lists.
 *
 * Apache mime.types file is currently used only with file.php plugin, so if
 * you don't use this plugin, you can safely skip this configuration option.
 */

$fm_cfg['res']['mime_types'] = array(
		'/etc/httpd/conf/apache-mime.types',		// Linux Mandrake 8.2
		'/etc/htdig/mime.types',
		'/usr/lib/mime.types',
		'/var/lib/apache/conf/mime.types',			// Slackware
		'/var/lib/apache/conf/mime.types.default',
		'/etc/mime.types',							// Debian
		'/etc/apache/mime.types',
		);

/*
 * Colors constats
 *
 * Rows background colors specifications. If not specified no background
 * color will be used for appropriate rows.
 *
 * Example:
 *   #add8e6 - lightblue
 *   #a0c9c9 - gray-green
 */

$fm_cfg['color']['even'] = '#CCCCCC';
//$fm_cfg['color']['odd']  = '#C7E7CC';
$fm_cfg['color']['odd']  = '#FFFFFF';

/*
 * Textarea size
 *
 * The number of rows and columns used in textarea
 * on the file edit page.
 */

$fm_cfg['textarea']['rows'] = 15;
$fm_cfg['textarea']['cols'] = 50;

/* Modeline for ViM {{{
 * vim: set ts=4:
 * vim600: tw=70 fdm=marker fdl=0 fdc=0:
 * }}} */

?>
