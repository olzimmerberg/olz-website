<?php

class OlzApi {
    public $endpoints = [];

    public function __construct() {
        $this->endpoints = [
            'onDaily' => function () {
                require_once __DIR__.'/endpoints/OnDailyEndpoint.php';
                $endpoint = new OnDailyEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../fetchers/SolvFetcher.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../tasks/SyncSolvTask.php';
                    require_once __DIR__.'/../utils/date/LiveDateUtils.php';
                    $date_utils = new LiveDateUtils();
                    $sync_solv_task = new SyncSolvTask($entityManager, new SolvFetcher(), $date_utils);
                    $sync_solv_task->setDefaultFileLogger();
                    $sync_solv_task->run();
                    $endpoint->setSyncSolvTask($sync_solv_task);
                    $endpoint->setDateUtils($date_utils);
                    $endpoint->setServerConfig($_CONFIG);
                });
                return $endpoint;
            },
            'onContinuously' => function () {
                require_once __DIR__.'/endpoints/OnContinuouslyEndpoint.php';
                $endpoint = new OnContinuouslyEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../utils/date/LiveDateUtils.php';
                    $date_utils = new LiveDateUtils();
                    $endpoint->setDateUtils($date_utils);
                    $endpoint->setServerConfig($_CONFIG);
                });
                return $endpoint;
            },
            'login' => function () {
                require_once __DIR__.'/endpoints/LoginEndpoint.php';
                $endpoint = new LoginEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'logout' => function () {
                require_once __DIR__.'/endpoints/LogoutEndpoint.php';
                $endpoint = new LogoutEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'updateUser' => function () {
                require_once __DIR__.'/endpoints/UpdateUserEndpoint.php';
                $endpoint = new UpdateUserEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'updatePassword' => function () {
                require_once __DIR__.'/endpoints/UpdateUserPasswordEndpoint.php';
                $endpoint = new UpdateUserPasswordEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'signUpWithPassword' => function () {
                require_once __DIR__.'/endpoints/SignUpWithPasswordEndpoint.php';
                $endpoint = new SignUpWithPasswordEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'loginWithStrava' => function () {
                require_once __DIR__.'/endpoints/LoginWithStravaEndpoint.php';
                $endpoint = new LoginWithStravaEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/StravaUtils.php';
                    $strava_utils = getStravaUtilsFromEnv();
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setStravaUtils($strava_utils);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'signUpWithStrava' => function () {
                require_once __DIR__.'/endpoints/SignUpWithStravaEndpoint.php';
                $endpoint = new SignUpWithStravaEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'linkTelegram' => function () {
                require_once __DIR__.'/endpoints/LinkTelegramEndpoint.php';
                $endpoint = new LinkTelegramEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/notify/TelegramUtils.php';
                    $telegram_utils = getTelegramUtilsFromEnv();
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setTelegramUtils($telegram_utils);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'onTelegram' => function () {
                require_once __DIR__.'/endpoints/OnTelegramEndpoint.php';
                $endpoint = new OnTelegramEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../utils/notify/TelegramUtils.php';
                    $telegram_utils = getTelegramUtilsFromEnv();
                    $endpoint->setTelegramUtils($telegram_utils);
                    $endpoint->setServerConfig($_CONFIG);
                });
                return $endpoint;
            },
            'getLogs' => function () {
                require_once __DIR__.'/endpoints/GetLogsEndpoint.php';
                $endpoint = new GetLogsEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
        ];
    }
}
