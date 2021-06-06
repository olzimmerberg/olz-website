<?php

$olz_api_anmelden = [
    'createRegistration' => function () {
        require_once __DIR__.'/endpoints/CreateRegistrationEndpoint.php';
        $endpoint = new CreateRegistrationEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $_CONFIG, $_DATE, $entityManager;
            require_once __DIR__.'/../config/date.php';
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            $endpoint->setEntityManager($entityManager);
            $endpoint->setDateUtils($_DATE);
        });
        return $endpoint;
    },
    'createRegistrationForm' => function () {
        require_once __DIR__.'/endpoints/CreateRegistrationFormEndpoint.php';
        $endpoint = new CreateRegistrationFormEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $_CONFIG, $_DATE, $entityManager;
            require_once __DIR__.'/../config/date.php';
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            $endpoint->setEntityManager($entityManager);
            $endpoint->setDateUtils($_DATE);
        });
        return $endpoint;
    },
    'getManagedUsers' => function () {
        require_once __DIR__.'/endpoints/GetManagedUsersEndpoint.php';
        $endpoint = new GetManagedUsersEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $entityManager;
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            $endpoint->setEntityManager($entityManager);
        });
        return $endpoint;
    },
    'getRegistrationForm' => function () {
        require_once __DIR__.'/endpoints/GetRegistrationFormEndpoint.php';
        $endpoint = new GetRegistrationFormEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $entityManager;
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            $endpoint->setEntityManager($entityManager);
        });
        return $endpoint;
    },
];
