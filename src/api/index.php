<?php

require_once __DIR__.'/common/api.php';
require_once __DIR__.'/common/Endpoint.php';
require_once __DIR__.'/common/validate.php';
require_once __DIR__.'/../utils/auth/StravaUtils.php';
require_once __DIR__.'/../utils/session/StandardSession.php';

$endpoint_name = sanitized_endpoint_name_from_path_info($_SERVER['PATH_INFO']);

$raw_input = file_get_contents('php://input');
$parsed_input = json_decode($raw_input, true);
if ($parsed_input === null) {
    $parsed_input = [];
}
foreach ($_GET as $key => $value) {
    $parsed_input[$key] = $value;
}

try {
    $output = call_api($endpoint_name, $parsed_input);
    api_respond(200, $output);
} catch (HttpError $httperr) {
    api_respond($httperr->getCode(), $httperr->getStructuredAnswer());
}

function call_api($endpoint_name, $input) {
    switch ($endpoint_name) {
        case 'login':
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/endpoints/LoginEndpoint.php';
            $endpoint = new LoginEndpoint($entityManager);
            $endpoint->setSession(new StandardSession());
            break;

        case 'logout':
            require_once __DIR__.'/endpoints/LogoutEndpoint.php';
            $endpoint = new LogoutEndpoint();
            $endpoint->setSession(new StandardSession());
            break;

        case 'updateUser':
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/endpoints/UpdateUserEndpoint.php';
            $endpoint = new UpdateUserEndpoint($entityManager);
            $endpoint->setSession(new StandardSession());
            break;

        case 'updatePassword':
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/endpoints/UpdateUserPasswordEndpoint.php';
            $endpoint = new UpdateUserPasswordEndpoint($entityManager);
            $endpoint->setSession(new StandardSession());
            break;

        case 'loginWithStrava':
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/endpoints/LoginWithStravaEndpoint.php';
            $strava_utils = getStravaUtilsFromEnv();
            $endpoint = new LoginWithStravaEndpoint($entityManager, $strava_utils);
            $endpoint->setSession(new StandardSession());
            break;

        case 'signUpWithStrava':
            require_once __DIR__.'/../config/doctrine_db.php';
            require_once __DIR__.'/../model/index.php';
            require_once __DIR__.'/endpoints/SignUpWithStravaEndpoint.php';
            $endpoint = new SignUpWithStravaEndpoint($entityManager);
            $endpoint->setSession(new StandardSession());
            break;

        default:
            throw new HttpError(400, 'Invalid endpoint');
    }
    $endpoint->setServer($_SERVER);
    return $endpoint->call($input);
}
