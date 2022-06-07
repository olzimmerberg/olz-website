<?php

namespace Olz;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyBridge {
    public static function prepareLegacyScript(Request $request, Response $response, string $publicDirectory): ?string {
        // If Symfony successfully handled the route, you do not have to do anything.
        if ($response->isNotFound() === false) {
            return null;
        }

        // Figure out how to map to the needed script file
        // from the existing application and possibly (re-)set
        // some env vars.
        $path = $request->getPathInfo();
        $script_path = self::getLegacyScript(__DIR__."/../_/{$path}");
        if ($script_path !== null) {
            return self::useLegacyScript($script_path);
        }
        $res = preg_match('/^(.+\.php)(\/.*)$/', $path, $matches);
        if ($res) {
            $script_path = self::getLegacyScript(__DIR__."/../_/{$matches[1]}");
            if ($script_path !== null) {
                $_SERVER["PATH_INFO"] = $matches[2];
                return self::useLegacyScript($script_path);
            }
        }

        return null;
    }

    private static function getLegacyScript($script_path) {
        if (is_file($script_path)) {
            return $script_path;
        }
        $index_php_path = "{$script_path}/index.php";
        if (is_file($index_php_path)) {
            return $index_php_path;
        }
        $index_html_path = "{$script_path}/index.html";
        if (is_file($index_html_path)) {
            return $index_html_path;
        }
        return null;
    }

    private static function useLegacyScript($script_path) {
        $_SERVER["SCRIPT_FILENAME"] = realpath($script_path);
        $_SERVER["SCRIPT_NAME"] = realpath($script_path);
        $_SERVER["PHP_SELF"] = substr(realpath($script_path), strlen(realpath(__DIR__.'/../_')));
        return $script_path;
    }
}
