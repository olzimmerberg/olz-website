<?php

/**************************************************************************
 *
 * file.php
 *
 * This is a plugin for phpWebFileManager that allows you to restrict
 * access to View files.  
 *
 * Although it wouldn't technically be required, it works best to set up a
 * mod_rewrite rule in apache so that when a user choses to download a file
 * their browser doesn't ask them to save "file.php", but the actual file
 * name.  You can set up mod_rewrite like this:
 *
 * RewriteEngine On RewriteRule ^/fm/file(.*)$ /fm/file.php?fm_path=$1 [L]
 *
 * where /fm/ is where phpWebFileManager is installed.
 *
 * Then change $fm_cfg['url']['root'] variable in your config.inc.php file
 * to be "file/". If you can't use mod_rewrite for whatever reason,
 * set $fm_cfg['url']['root'] to "file.php?fm_path=".
 *
 * Tony J. White, tjw@tjw.org, 2002-04-15
 **************************************************************************/

/* $Platon: phpWebFileManager/plugins/file.php,v 1.8 2004/07/13 16:52:02 nepto Exp $ */

/*
 * phpWebFileManager initialization
 *
 * Config file is read from init.inc.php file.
 */

$fm_init_file = dirname(__FILE__)
    . (strlen(dirname(__FILE__)) > 0 ? '/' : '')
    . '../init.inc.php';

    if (! @file_exists($fm_init_file)) {
        exit;
    }

require_once $fm_init_file;
require_once $PN_PathPrefix . 'functions.inc.php';

/*
 * Moving from plugins/ subdirectory to phpWebFileManager root directory
 */

$fm_file_plugin_orig_dir = getcwd();
chdir('../');

/*
 * Main job
 */

fm_main();

/*
 * Moving back, just for certainty and security
 */

chdir($fm_file_plugin_orig_dir);


/*******************
  F U N C T I O N S
 *******************/

function fm_parse_apache_mime_types($file)
{
    $mimes_by_ext = array();
    $mime_types   = file($file);

    if (is_array($mime_types)) {
        while (list(,$line) = each($mime_types)) {
            if ($line[0] == '#')
                continue;

            $parts = preg_split('/(\s+)/', $line);
            if (is_array($parts) && sizeof($parts) > 2) {
                list(,$mime) = each($parts);
                while (list(,$ext) = each($parts)) {
                    if (trim($ext) != '')
                        $mimes_by_ext[$ext] = $mime;
                }
            }
        }
    }

    return $mimes_by_ext;
}

function fm_main()
{
    global $fm_cfg;
    global $HTTP_GET_VARS;

    /*
     * Permission check
     */

    if (! $fm_cfg['perm']['file']['view']) {
        echo 'Insufficient privileges for file viewing.';
        return false;
    }

    /*
     * Creating $file variable from CGI $fm_path variable
     */

    if (! isset($HTTP_GET_VARS[$fm_cfg['cgi'].'path'])) {
        echo 'CGI variable "'.$fm_cfg['cgi'].'path" is not present.';
        return false;
    } 

    $file = $HTTP_GET_VARS[$fm_cfg['cgi'].'path'];
    if ($file{0} != '/') {
        echo 'Incorrect "'.$fm_cfg['cgi'].'path" CGI variable.';
        return false;
    }

    $file = substr($file, 1);
    $file = preg_replace('|\.\./|', '', $file);
    $file = preg_replace('|\.\.$|', '', $file);
    $file = fm_append_slash($fm_cfg['dir']['root']).$file;

    /*
     * Testing file
     */

    if (! @file_exists($file) || ! @is_readable($file)) {
        echo 'File "'.$file.'" not found or is not readable.';
        return false;
    }

    /*
     * Parsing of apache-mime.types file
     */

    $mimes_by_ext = array();
    foreach ($fm_cfg['res']['mime_types'] as $mimes_fname) {
        if (@file_exists($mimes_fname) && @is_readable($mimes_fname)) {
            $mimes_by_ext = fm_parse_apache_mime_types($mimes_fname);
            break;
        }
    }

    /*
     * Writting file on output
     */

    if (preg_match('/.*\.([a-z]{2,5})$/i', $file, $matches)
            && trim($mimes_by_ext[$matches[1]]) != '') {
        header('Content-Type: '.trim($mimes_by_ext[$matches[1]]));
    } else {
        header('Content-Type: text/plain');
    }
    header('Content-Length: '.filesize($file));
    header('Content-Disposition: attachment; filename="'.basename($file).'"');

    @readfile($file);
    return true;
}

?>
