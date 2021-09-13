<?php

class OlzApi {
    public $endpoints = [];

    public function __construct() {
        global $olz_api_anmelden, $olz_api_news;
        require_once __DIR__.'/../anmelden/olz_api.php';
        require_once __DIR__.'/../news/olz_api.php';
        $olz_api = [
            'onDaily' => function () {
                require_once __DIR__.'/endpoints/OnDailyEndpoint.php';
                $endpoint = new OnDailyEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG, $_DATE, $entityManager;
                    require_once __DIR__.'/../config/date.php';
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../fetchers/SolvFetcher.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../tasks/CleanTempDirectoryTask.php';
                    require_once __DIR__.'/../tasks/SyncSolvTask.php';
                    $date_utils = $_DATE;
                    $clean_temp_directory_task = new CleanTempDirectoryTask($date_utils, $_CONFIG);
                    $sync_solv_task = new SyncSolvTask($entityManager, new SolvFetcher(), $date_utils, $_CONFIG);
                    $endpoint->setCleanTempDirectoryTask($clean_temp_directory_task);
                    $endpoint->setSyncSolvTask($sync_solv_task);
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setDateUtils($date_utils);
                    $endpoint->setEnvUtils($_CONFIG);
                });
                return $endpoint;
            },
            'onContinuously' => function () {
                require_once __DIR__.'/endpoints/OnContinuouslyEndpoint.php';
                $endpoint = new OnContinuouslyEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG, $_DATE, $entityManager;
                    require_once __DIR__.'/../config/date.php';
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../tasks/ProcessEmailTask.php';
                    require_once __DIR__.'/../tasks/SendDailyNotificationsTask.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    require_once __DIR__.'/../utils/notify/EmailUtils.php';
                    require_once __DIR__.'/../utils/notify/TelegramUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $date_utils = $_DATE;
                    $email_utils = EmailUtils::fromEnv();
                    $telegram_utils = TelegramUtils::fromEnv();
                    $process_email_task = new ProcessEmailTask($entityManager, $auth_utils, $email_utils, $date_utils, $_CONFIG);
                    $send_daily_notifications_task = new SendDailyNotificationsTask($entityManager, $email_utils, $telegram_utils, $date_utils, $_CONFIG);
                    $endpoint->setProcessEmailTask($process_email_task);
                    $endpoint->setSendDailyNotificationsTask($send_daily_notifications_task);
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setDateUtils($date_utils);
                    $endpoint->setEnvUtils($_CONFIG);
                });
                return $endpoint;
            },
            'login' => function () {
                require_once __DIR__.'/endpoints/LoginEndpoint.php';
                $endpoint = new LoginEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
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
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'updatePassword' => function () {
                require_once __DIR__.'/endpoints/UpdateUserPasswordEndpoint.php';
                $endpoint = new UpdateUserPasswordEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'signUpWithPassword' => function () {
                require_once __DIR__.'/endpoints/SignUpWithPasswordEndpoint.php';
                $endpoint = new SignUpWithPasswordEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'loginWithStrava' => function () {
                require_once __DIR__.'/endpoints/LoginWithStravaEndpoint.php';
                $endpoint = new LoginWithStravaEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $entityManager;
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
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'executeEmailReaction' => function () {
                require_once __DIR__.'/endpoints/ExecuteEmailReactionEndpoint.php';
                $endpoint = new ExecuteEmailReactionEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/notify/EmailUtils.php';
                    $email_utils = EmailUtils::fromEnv();
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setEmailUtils($email_utils);
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
                    $endpoint->setEnvUtils($_CONFIG);
                });
                return $endpoint;
            },
            'getLogs' => function () {
                require_once __DIR__.'/endpoints/GetLogsEndpoint.php';
                $endpoint = new GetLogsEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/server.php';
                    $endpoint->setEnvUtils($_CONFIG);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'updateNotificationSubscriptions' => function () {
                require_once __DIR__.'/endpoints/UpdateNotificationSubscriptionsEndpoint.php';
                $endpoint = new UpdateNotificationSubscriptionsEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG, $_DATE, $entityManager;
                    require_once __DIR__.'/../config/date.php';
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    $date_utils = $_DATE;
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setDateUtils($date_utils);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'updateOlzText' => function () {
                require_once __DIR__.'/endpoints/UpdateOlzTextEndpoint.php';
                $endpoint = new UpdateOlzTextEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $entityManager;
                    require_once __DIR__.'/../config/doctrine_db.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEntityManager($entityManager);
                    $endpoint->setSession(new StandardSession());
                });
                return $endpoint;
            },
            'startUpload' => function () {
                require_once __DIR__.'/endpoints/StartUploadEndpoint.php';
                $endpoint = new StartUploadEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    require_once __DIR__.'/../utils/GeneralUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $general_utils = GeneralUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEnvUtils($_CONFIG);
                    $endpoint->setGeneralUtils($general_utils);
                });
                return $endpoint;
            },
            'updateUpload' => function () {
                require_once __DIR__.'/endpoints/UpdateUploadEndpoint.php';
                $endpoint = new UpdateUploadEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    require_once __DIR__.'/../utils/GeneralUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $general_utils = GeneralUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEnvUtils($_CONFIG);
                    $endpoint->setGeneralUtils($general_utils);
                });
                return $endpoint;
            },
            'finishUpload' => function () {
                require_once __DIR__.'/endpoints/FinishUploadEndpoint.php';
                $endpoint = new FinishUploadEndpoint();
                $endpoint->setSetupFunction(function ($endpoint) {
                    global $_CONFIG;
                    require_once __DIR__.'/../config/server.php';
                    require_once __DIR__.'/../model/index.php';
                    require_once __DIR__.'/../utils/auth/AuthUtils.php';
                    $auth_utils = AuthUtils::fromEnv();
                    $endpoint->setAuthUtils($auth_utils);
                    $endpoint->setEnvUtils($_CONFIG);
                });
                return $endpoint;
            },
        ];

        $this->endpoints = array_merge(
            $olz_api_anmelden,
            $olz_api_news,
            $olz_api
        );
    }
}
