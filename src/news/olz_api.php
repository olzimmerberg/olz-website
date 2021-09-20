<?php

$olz_api_news = [
    'createNews' => function () {
        require_once __DIR__.'/endpoints/CreateNewsEndpoint.php';
        $endpoint = new CreateNewsEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $_CONFIG, $_DATE, $entityManager;
            require_once __DIR__.'/../config/date.php';
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/../utils/auth/AuthUtils.php';
            require_once __DIR__.'/../utils/env/EnvUtils.php';
            $auth_utils = AuthUtils::fromEnv();
            $env_utils = EnvUtils::fromEnv();
            $endpoint->setAuthUtils($auth_utils);
            $endpoint->setDateUtils($_DATE);
            $endpoint->setEntityManager($entityManager);
            $endpoint->setEnvUtils($env_utils);
        });
        return $endpoint;
    },
];
