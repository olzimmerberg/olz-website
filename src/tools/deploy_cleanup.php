<?php

require_once __DIR__.'/common.php';

function deploy_cleanup($deploy_path) {
    $current_link_path = "{$deploy_path}current";
    $is_current_linked = is_link($current_link_path);
    if (!$is_current_linked) {
        throw new Exception('The current target is not a link.');
    }
    $current_link_target = realpath("{$current_link_path}/");
    $is_current_target_deployed = substr($current_link_target, 0, strlen($deploy_path)) === $deploy_path;
    if (!$is_current_target_deployed) {
        throw new Exception('Current target is pointing to somewhere outside the deploy path.');
    }
    $deployments = get_deployments($deploy_path);
    sort($deployments);
    // Leave the latest three deployments untouched
    array_pop($deployments);
    array_pop($deployments);
    array_pop($deployments);
    foreach ($deployments as $deployment) {
        $deployment_path = "{$deploy_path}{$deployment}";
        // Leave the current deployment untouched
        if ($deployment_path === $current_link_target) {
            continue;
        }
        remove_r($deployment_path);
        echo "Removed deployment: {$deployment_path}\n";
    }
}

function get_deployments($deploy_path) {
    $deploy_contents = scandir($deploy_path);
    $date_regex = '/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})_([0-9]{2})_([0-9]{2})_([0-9]{2})$/';
    $deployments = [];
    foreach ($deploy_contents as $entry) {
        $is_date = preg_match($date_regex, $entry, $date_matches);
        $entry_path = "{$deploy_path}{$entry}";
        if ($is_date && is_dir($entry_path)) {
            $deployments[] = $entry;
        }
    }
    return $deployments;
}
