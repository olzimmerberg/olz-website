<?php

namespace Olz\Apps;

use Olz\Entity\User;
use PhpTypeScriptApi\Api;

class OlzApps {
    public static function getApp(string $basename): ?BaseAppMetadata {
        $metadata_class_name = "\\Olz\\Apps\\{$basename}\\Metadata";
        if (class_exists($metadata_class_name)) {
            return new $metadata_class_name();
        }
        return null;
    }

    /** @return array<string> */
    public static function getAppPaths(): array {
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

    /** @return array<BaseAppMetadata> */
    public static function getApps(): array {
        $app_paths = self::getAppPaths();
        $apps = [];
        foreach ($app_paths as $app_path) {
            $app_basename = basename($app_path);
            $app = self::getApp($app_basename);
            if ($app !== null) {
                $apps[] = $app;
            }
        }
        return $apps;
    }

    /** @return array<BaseAppMetadata> */
    public static function getAppsForUser(?User $user): array {
        $apps = self::getApps();
        $apps_for_user = [];
        foreach ($apps as $app) {
            if ($app->isAccessibleToUser($user)) {
                $apps_for_user[] = $app;
            }
        }
        return $apps_for_user;
    }

    public static function registerAllEndpoints(Api $api): void {
        $app_paths = self::getAppPaths();
        foreach ($app_paths as $app_path) {
            $app_basename = basename($app_path);
            $endpoints_class_name = "\\Olz\\Apps\\{$app_basename}\\{$app_basename}Endpoints";
            if (class_exists($endpoints_class_name)) {
                $endpoints = new $endpoints_class_name();
                $endpoints->register($api);
            }
        }
    }
}
