<?php

$olz_api_webdav = [
    'getWebdavAccessToken' => function () {
        require_once __DIR__.'/endpoints/GetWebdavAccessTokenEndpoint.php';
        $endpoint = new GetWebdavAccessTokenEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $_CONFIG, $_DATE, $entityManager;
            require_once __DIR__.'/../config/date.php';
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/../utils/auth/AuthUtils.php';
            require_once __DIR__.'/../utils/GeneralUtils.php';
            $auth_utils = AuthUtils::fromEnv();
            $general_utils = GeneralUtils::fromEnv();
            $endpoint->setAuthUtils($auth_utils);
            $endpoint->setDateUtils($_DATE);
            $endpoint->setEntityManager($entityManager);
            $endpoint->setGeneralUtils($general_utils);
        });
        return $endpoint;
    },
    'revokeWebdavAccessToken' => function () {
        require_once __DIR__.'/endpoints/RevokeWebdavAccessTokenEndpoint.php';
        $endpoint = new RevokeWebdavAccessTokenEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $_CONFIG, $entityManager;
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/../utils/auth/AuthUtils.php';
            $auth_utils = AuthUtils::fromEnv();
            $endpoint->setAuthUtils($auth_utils);
            $endpoint->setEntityManager($entityManager);
        });
        return $endpoint;
    },
];
