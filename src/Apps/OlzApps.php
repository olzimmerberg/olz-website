<?php

namespace Olz\Apps;

use Olz\Apps\Anmelden\AnmeldenEndpoints;
use Olz\Apps\Commands\CommandsEndpoints;
use Olz\Apps\Files\FilesEndpoints;
use Olz\Apps\Logs\LogsEndpoints;
use Olz\Apps\Members\MembersEndpoints;
use Olz\Apps\Monitoring\MonitoringEndpoints;
use Olz\Apps\Newsletter\NewsletterEndpoints;
use Olz\Apps\Oev\OevEndpoints;
use Olz\Apps\Panini2024\Panini2024Endpoints;
use Olz\Apps\Quiz\QuizEndpoints;
use Olz\Apps\Results\ResultsEndpoints;
use Olz\Apps\SearchEngines\SearchEnginesEndpoints;
use Olz\Apps\Statistics\StatisticsEndpoints;
use Olz\Apps\Youtube\YoutubeEndpoints;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Api;

class OlzApps {
    /** @var array<BaseAppEndpoints> */
    protected array $apps_endpoints;

    public function __construct(
        AnmeldenEndpoints $anmeldenEndpoints,
        CommandsEndpoints $commandsEndpoints,
        FilesEndpoints $filesEndpoints,
        LogsEndpoints $logsEndpoints,
        MembersEndpoints $membersEndpoints,
        MonitoringEndpoints $monitoringEndpoints,
        NewsletterEndpoints $newsletterEndpoints,
        OevEndpoints $oevEndpoints,
        Panini2024Endpoints $panini2024Endpoints,
        QuizEndpoints $quizEndpoints,
        ResultsEndpoints $resultsEndpoints,
        SearchEnginesEndpoints $searchEnginesEndpoints,
        StatisticsEndpoints $statisticsEndpoints,
        YoutubeEndpoints $youtubeEndpoints,
    ) {
        $this->apps_endpoints = [
            $anmeldenEndpoints,
            $commandsEndpoints,
            $filesEndpoints,
            $logsEndpoints,
            $membersEndpoints,
            $monitoringEndpoints,
            $newsletterEndpoints,
            $oevEndpoints,
            $panini2024Endpoints,
            $quizEndpoints,
            $resultsEndpoints,
            $searchEnginesEndpoints,
            $statisticsEndpoints,
            $youtubeEndpoints,
        ];
    }

    public static function getApp(string $basename): ?BaseAppMetadata {
        $metadata_class_name = "\\Olz\\Apps\\{$basename}\\Metadata";
        if (class_exists($metadata_class_name)) {
            $instance = new $metadata_class_name();
            if ($instance instanceof BaseAppMetadata) {
                return $instance;
            }
        }
        return null;
    }

    /** @return array<string> */
    public static function getAppPaths(): array {
        $entries = scandir(__DIR__) ?: [];
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

    public function registerAllEndpoints(Api $api): void {
        foreach ($this->apps_endpoints as $apps_endpoints) {
            $apps_endpoints->register($api);
        }
    }
}
