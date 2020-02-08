<?php

/**************************************************************************
 *
 * auth.php
 *
 * This is a plugin for phpWebFileManager that allows you to restrict
 * access to phpWebFileManager script using HTTP authentication. Edit
 * username and password constants below.
 *
 **************************************************************************/

/* $Platon: phpWebFileManager/plugins/auth.php,v 1.3 2003/12/15 13:48:25 nepto Exp $ */

if (! isset($PHP_AUTH_USER) || ! isset($PHP_AUTH_PW)
        || $PHP_AUTH_USER != 'web276' || $PHP_AUTH_PW != '7Q7_$*</q8Ru') {

    header('WWW-Authenticate: Basic realm="phpWebFileManager authentication"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<center><h1>Authentication required</h1></center>';
    exit;

} else {
    header('WWW-Authenticate: Basic '.base64_encode($username . ":" . $password));

    // sucessfully authenticated

}

?>
