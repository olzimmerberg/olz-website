<?php

/*
 * phpWebFileManager - simple file management PHP script
 *
 * functions.inc.php - functions file
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

/* $Platon: phpWebFileManager/functions.inc.php,v 1.8 2005/10/01 17:17:34 nepto Exp $ */

/* Functions in this file was extracted created from Platon class.

   Class Platon is part of Platon SDG PHP framework and can be accessible via
   phpPlatonLib project. */

/**
 * Returns value of CGI variable.
 *
 * @param   string  $name           CGI variable name
 * @param   mixed   $default_value  what to return if variable not present
 * @return                          stripslashed value of CGI variable
 */
function fm_get_cgi_var($name, $default_value = null) /* {{{ */
{
    static $magic_quotes_gpc = null;
    if ($magic_quotes_gpc === null) {
        $magic_quotes_gpc = get_magic_quotes_gpc();
    }
    $var = @$_GET[$name];
    if (! isset($var)) {
        $var = @$_POST[$name];
    }
    if (isset($var)) {
        if ($magic_quotes_gpc) {
            if (is_array($var)) {
                foreach (array_keys($var) as $key) {
                    $var[$key] = stripslashes($var[$key]);
                }
            } else {
                $var = stripslashes($var);
            }
        }
    } else {
        $var = @$default_value;
    }
    return $var;
} /* }}} */

/**
 * Returns original variables HTML code for use in forms or links.
 *
 * @param   mixed   $origvars  string or array of original variables;
 *                             if string values should be rawurlencoded();
 *                             if array values should not be encoded
 * @param   string  $method    type of method ("POST" or "GET")
 * @param   mixed   $default   default value of variables
 *                             if null, empty values will be skipped
 * @return                     get HTML code of original variables
 */
function fm_get_origvars_html($origvars, $method = 'POST', $default = '') /* {{{ */
{
    $ret    = '';
    $method = strtoupper($method);
    if ($method == 'POST') {
        if (! is_array($origvars)) {
            $new_origvars = array();
            foreach (explode('&', $origvars) as $param) {
                $parts = explode('=', $param, 2);
                if (! isset($parts[1])) {
                    $parts[1] = $default;
                }
                if (strlen($parts[0]) <= 0) {
                    continue;
                }
                $new_origvars[$parts[0]] = $parts[1];
            }
            $origvars =& $new_origvars;
        }
        foreach ($origvars as $key => $val) {
            if (strlen($val) <= 0 && $default === null) {
                continue;
            }
            $key = rawurldecode($key);
            $val = rawurldecode($val);
            $ret .= '<input type="hidden" name="';
            $ret .= htmlspecialchars($key).'"';
            $ret .= ' value="'.htmlspecialchars($val).'"';
            $ret .= " />\n";
        }
    } else if (! strncmp('GET', $method, 3)) {
        if (! is_array($origvars)) {
            $ret .= $origvars;
        } else {
            foreach ($origvars as $key => $val) {
                if (strlen($val) <= 0 && $default === null) {
                    continue;
                }
                $ret == '' || $ret .= '&amp;';
                $ret .= htmlspecialchars(rawurlencode($key));
                $ret .= '=';
                $ret .= htmlspecialchars(rawurlencode($val));
            }
        }
        if ($method[strlen($method) - 1] == '+') {
            $ret = "?$ret";
        }
    } else {
        trigger_error('Unsupported Platon::get_origvars_html() method: '
                .$method, E_USER_ERROR);
    }
    return $ret;
} /* }}} */

/** 
 * Appends slash at the end of string if it is not empty and slash is not
 * already present at the end of string.
 *
 * @param   string  $string     string to search
 * @param   char    $char       character to append, slash by default
 * @return                      changed string on success, false on failure
 */
function fm_append_slash($string, $char = '/') /* {{{ */
{
    if (! is_string($string) || ! is_string($char)) {
        return false;
    }
    if (strlen($string) > 0) {
        if ($string[strlen($string) - 1] != $char[0]) 
            $string .= $char[0];
    }
    return $string;
} /* }}} */

/**
 * Returns passed path without scriptname; method also accepts array
 * returned from Platon::glue_url() method.
 *
 * @param   mixed   $url    string URL or array from Platon::glue_url()
 * @return  string          path without scriptname
 */
function fm_get_path_without_scriptname($url) /* {{{ */
{
    $path = is_array($url) ? $url['path'] : $url;
    $path = preg_replace('/http[s]?:\/\/[^\/]+/', '', $path);
    $path = preg_replace('/\?.*/', '', $path);
    $path = preg_replace('/[^\/]+\z/', '', $path);
    return $path;
} /* }}} */

/** 
 * Creates URL string from components; components are array as it is
 * returned from glue_url() function.
 *
 * @param   array   $url    components array
 * @return  string          glued url
 * @author  Tomas V.V.Cox <cox@idecnet.com>
 * @date    19/Feb/2001
 */
function fm_glue_url($url) /* {{{ */
{ 
    if (! is_array($url))
        return false; 
    // scheme 
    $uri = empty($url['scheme']) ? '' : $url['scheme'] . '://';
    // user and pass 
    if (! empty($url['user'])) { 
        $uri .= $url['user'].':'.$url['pass'].'@'; 
    } 
    // host 
    $uri .= $url['host']; 
    // port 
    $port = empty($url['port']) ? '' : ':'.$url['port']; 
    $uri .= $port; 
    // path 
    $uri .= $url['path']; 
    // fragment or query 
    if (isset($url['fragment'])) { 
        $uri .= '#' . $url['fragment']; 
    } 
    elseif (isset($url['query'])) {  
        $uri .= '?' . $url['query']; 
    } 
    return $uri; 
} /* }}} */

/* vim: set expandtab tabstop=4 shiftwidth=4:
 * vim600: fdm=marker fdl=0 fdc=0
 */

?>
