<?php

namespace Olz\Apps;

class OlzApps {
    public static function getAppPaths() {
        $entries = scandir(__DIR__);
        $app_paths = [];
        foreach ($entries as $entry) {
            $path = __DIR__."/{$entry}";
            if (is_dir($path) && is_file("{$path}/Metadata.php")) {
                $app_paths[] = $path;
            }
        }
        return $app_paths;
    }

    public static function getApps() {
        $app_paths = self::getAppPaths();
        $apps = [];
        foreach ($app_paths as $key => $app_path) {
            $app_basename = basename($app_path);
            $metadata_class_name = "\\Olz\\Apps\\{$app_basename}\\Metadata";
            if (class_exists($metadata_class_name)) {
                $apps[] = new $metadata_class_name();
            }
        }
        return $apps;
    }

    public static function getAppsForUser($user) {
        $apps = self::getApps();
        $apps_for_user = [];
        foreach ($apps as $app) {
            if ($app->isAccessibleToUser($user)) {
                $apps_for_user[] = $app;
            }
        }
        return $apps_for_user;
    }
}
