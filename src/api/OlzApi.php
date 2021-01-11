<?php

class OlzApi {
    public $endpoints = [];

    public function __construct() {
        $this->endpoints = [
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
        ];
    }
}
