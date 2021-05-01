<?php

require_once __DIR__.'/common/api.php';
require_once __DIR__.'/common/Endpoint.php';
require_once __DIR__.'/../config/init.php';
require_once __DIR__.'/../utils/auth/StravaUtils.php';
require_once __DIR__.'/../utils/env/EnvUtils.php';
require_once __DIR__.'/../utils/session/StandardSession.php';

$endpoint_name = sanitized_endpoint_name_from_path_info($_SERVER['PATH_INFO']);

try {
    $output = call_api($endpoint_name);
    api_respond(200, $output);
} catch (HttpError $httperr) {
    api_respond($httperr->getCode(), $httperr->getStructuredAnswer());
}

function call_api($endpoint_name) {
    require_once __DIR__.'/OlzApi.php';
    $olz_api = new OlzApi();
    if (!isset($olz_api->endpoints[$endpoint_name])) {
        throw new HttpError(400, 'Invalid endpoint');
    }
    $logger = EnvUtils::fromEnv()->getLogger("Endpoint:{$endpoint_name}");
    EnvUtils::activateLogger($logger);
    $endpoint = $olz_api->endpoints[$endpoint_name]();
    $endpoint->setServer($_SERVER);
    $endpoint->setLogger($logger);
    $endpoint->setup();
    $input = $endpoint->parseInput();
    $result = $endpoint->call($input);
    EnvUtils::deactivateLogger($logger);
    return $result;
}
