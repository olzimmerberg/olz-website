<?php

function api_respond($http_code, $response) {
    http_response_code($http_code);
    exit(json_encode($response));
}

function sanitized_endpoint_name_from_path_info($path_info) {
    $has_path_info = preg_match('/^\/([a-zA-Z0-9]+)$/', $path_info, $path_info_matches);
    if (!$has_path_info) {
        api_respond(400, 'No path info');
    }
    return $path_info_matches[1];
}
