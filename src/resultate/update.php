<?php

require_once __DIR__.'/../config/paths.php';

$filename = $_GET['file'];
$is_filename_ok = preg_match('/^[a-z0-9\-\.]+$/', $filename);
if (!$is_filename_ok) {
    die('Filename must match ^[a-z0-9\-\.]+$');
}
$results_data_path = realpath("{$data_path}results");
$file_path = "{$results_data_path}/{$filename}";
if (isset($_POST['new'])) {
    if (is_file($file_path)) {
        rename($file_path, $file_path.".bak_".olz_current_date("Y-m-dTH:i:s"));
    }
    $new_content = base64_decode($_POST['new']);
    if (!$new_content) {
        die(json_encode([false, "Invalid base64 data"]));
    }
    file_put_contents($file_path, $new_content);
    file_put_contents(
        "{$results_data_path}/_live.json",
        json_encode([
            'file' => $filename,
            'last_updated_at' => olz_current_date('Y-m-d H:i:s'),
        ]),
    );
    die(json_encode([true, ""]));
}
if ($file_path) {
    die("OK");
}
