<?php

require_once __DIR__.'/common/api.php';
require_once __DIR__.'/common/Endpoint.php';
require_once __DIR__.'/common/validate.php';
require_once __DIR__.'/../utils/session/StandardSession.php';

$endpoint_name = sanitized_endpoint_name_from_path_info($_SERVER['PATH_INFO']);

$raw_input = file_get_contents('php://input');
$parsed_input = json_decode($raw_input, true) || [];
foreach ($_GET as $key => $value) {
    $parsed_input[$key] = $value;
}

try {
    $output = call_api($endpoint_name, $parsed_input);
    api_respond(200, $output);
} catch (HttpError $httperr) {
    api_respond($httperr->getCode(), $httperr->getMessage());
}

function call_api($endpoint_name, $input) {
    switch ($endpoint_name) {
        case 'login':
            require_once __DIR__.'/../config/doctrine.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/endpoints/LoginEndpoint.php';
            $endpoint = new LoginEndpoint($entityManager);
            $endpoint->setSession(new StandardSession());
            break;
        default:
            throw new HttpError(400, 'Invalid endpoint');
    }
    $endpoint->setServer($_SERVER);
    return $endpoint->call($input);
}
