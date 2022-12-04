<?php

function backup_monitoring() {
    $ch = curl_init();
    $user_agent_string = "Mozilla/5.0 (compatible; backup_monitoring/2.1; +https://github.com/olzimmerberg/olz-website/blob/main/_/tools/monitoring/backup_monitoring.php)";

    curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/olzimmerberg/olz-website/actions/workflows/ci-scheduled.yml/runs?page=1&per_page=3&status=completed");
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $completed_runs_raw = curl_exec($ch);

    $completed_runs = json_decode($completed_runs_raw, true);
    if (!$completed_runs) {
        throw new \Exception("No completed runs JSON");
    }
    $workflow_runs = $completed_runs['workflow_runs'] ?? null;
    if (!$workflow_runs) {
        throw new \Exception("No workflow_runs");
    }
    if (count($workflow_runs) !== 3) {
        throw new \Exception("Expected exactly 3 workflow runs");
    }
    $has_successful = false;
    $errors = '';
    foreach ($workflow_runs as $workflow_run) {
        try {
            check_workflow_run();
            $has_successful = true;
        } catch (\Throwable $th) {
            $errors .= "  ".var_export($th, true)."\n";
        }
    }
    if ($has_successful) {
        echo "OK:";
    } else {
        throw new \Exception("All 3 backup runs have problems:\n {$errors}");
    }
}

function check_workflow_run($workflow_run) {
    if ($workflow_run['name'] !== 'CI:scheduled') {
        throw new \Exception("Expected workflow_run name to be CI:scheduled");
    }
    if ($workflow_run['head_branch'] !== 'main') {
        throw new \Exception("Expected workflow_run head_branch to be main");
    }
    if ($workflow_run['status'] !== 'completed') {
        throw new \Exception("Expected workflow_run status to be completed");
    }
    $now = new \DateTime();
    $minus_two_days = \DateInterval::createFromDateString("-2 days");
    $two_days_ago = $now->add($minus_two_days);
    if (strtotime($workflow_run['created_at'] ?? '') < $two_days_ago->getTimestamp()) {
        throw new \Exception("Expected workflow_run created_at to be in the last 2 days");
    }
    if ($workflow_run['conclusion'] !== 'success') {
        throw new \Exception("Expected workflow_run conclusion to be success");
    }
    if (!$workflow_run['artifacts_url']) {
        throw new \Exception("Expected workflow_run artifacts_url");
    }

    curl_setopt($ch, CURLOPT_URL, $workflow_run['artifacts_url']);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $artifacts_raw = curl_exec($ch);

    curl_close($ch);

    $artifacts_dict = json_decode($artifacts_raw, true);
    if (!$artifacts_dict) {
        throw new \Exception("No artifacts JSON");
    }
    $artifacts = $artifacts_dict['artifacts'] ?? null;
    if (!$artifacts) {
        throw new \Exception("No artifacts");
    }
    if (count($artifacts) !== 1) {
        throw new \Exception("Expected exactly 1 artifact");
    }
    $artifact = $artifacts[0];
    if ($artifact['name'] !== 'backup') {
        throw new \Exception("Expected artifact name to be backup");
    }
    if ($artifact['expired'] !== false) {
        throw new \Exception("Expected artifact expired to be false");
    }
}
