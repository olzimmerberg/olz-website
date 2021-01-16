<?php

require_once __DIR__.'/common/api.php';
require_once __DIR__.'/common/Endpoint.php';
require_once __DIR__.'/common/validate.php';
require_once __DIR__.'/../utils/auth/StravaUtils.php';
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
    $endpoint = $olz_api->endpoints[$endpoint_name]();
    $endpoint->setServer($_SERVER);
    $endpoint->setDefaultFileLogger();
    $endpoint->setup();
    $input = $endpoint->parseInput();
    return $endpoint->call($input);
}
