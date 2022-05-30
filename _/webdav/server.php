<?php

use Sabre\DAV;

require_once __DIR__.'/../config/doctrine_db.php';
require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../utils/auth/AuthUtils.php';
require_once __DIR__.'/CallbackAuthBackend.php';

error_reporting(0);

// Hack: Use weird path-token-authentication
// Reason: Hoststar cannot have basic auth header ¯\_(ツ)_/¯
$path_info = $_SERVER['PATH_INFO'];
$pattern = '/^(\/?access_token__([a-zA-Z0-9_\-]+))(\/.*)?$/';
$res = preg_match($pattern, $path_info, $matches);
$stripped_path_info = $res ? $matches[1] : '';
$access_token = $res ? $matches[2] : null;
$simulated_path_info = $res ? $matches[3] : $path_info;
$_SERVER['PATH_INFO'] = $simulated_path_info;
// end of hack

// The user can be logged in by PHP session or access token.
$auth_utils = AuthUtils::fromEnv();
$auth_utils->setGetParams(['access_token' => $access_token]);
$user = $auth_utils->getAuthenticatedUser();
$user_root = $user ? $user->getRoot() : '';

$root_directory = new DAV\FS\Directory("{$data_path}OLZimmerbergAblage/{$user_root}");
$server = new DAV\Server($root_directory);
$server->setBaseUri("{$code_href}webdav/server.php{$stripped_path_info}");

$auth_backend = new DAV\Auth\Backend\CallbackAuthBackend(
    function () use ($auth_utils, $user) {
        $has_permission = $auth_utils->hasPermission('webdav', $user);
        if ($has_permission) {
            return [true, $user->getUsername()];
        }
        return [false, 'WebDAV permission denied'];
    }
);
$auth_plugin = new DAV\Auth\Plugin($auth_backend);
$server->addPlugin($auth_plugin);

$lock_backend = new DAV\Locks\Backend\File('data/locks');
$lock_plugin = new DAV\Locks\Plugin($lock_backend);
$server->addPlugin($lock_plugin);

$server->addPlugin(new DAV\Browser\Plugin());

$server->exec();
