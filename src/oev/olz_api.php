<?php

$olz_api_oev = [
    'searchTransportConnection' => function () {
        require_once __DIR__.'/endpoints/SearchTransportConnectionEndpoint.php';
        $endpoint = new SearchTransportConnectionEndpoint();
        $endpoint->setSetupFunction(function ($endpoint) {
            global $_CONFIG;
            require_once __DIR__.'/../fetchers/TransportApiFetcher.php';
            require_once __DIR__.'/../utils/auth/AuthUtils.php';
            $auth_utils = AuthUtils::fromEnv();
            $transport_api_fetcher = new TransportApiFetcher();
            $endpoint->setAuthUtils($auth_utils);
            $endpoint->setTransportApiFetcher($transport_api_fetcher);
        });
        return $endpoint;
    },
];
