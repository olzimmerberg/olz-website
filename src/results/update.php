<?php

require_once __DIR__.'/../config/paths.php';

$filename = $_GET['file'];
$is_filename_ok = preg_match('/^[a-z0-9\-\.]+$/', $filename);
if (!$is_filename_ok) {
    die('Filename must match ^[a-z0-9\-\.]+$');
}
$results_data_path = realpath("{$data_path}results/");
$file_path = realpath("{$results_data_path}{$filename}");
if (isset($_POST['diff'])) {
    if (!is_file($file_path)) {
        die(json_encode([false, "No such file: {$file_path}"]));
    }
    $diff_content = base64_decode($_POST['diff']);
    if (!$diff_content) {
        die(json_encode([false, "Invalid base64 data"]));
    }
    file_put_contents($file_path.".patch", $diff_content);
    $res = shell_exec("patch -u ".$file_path." -i ".$file_path.".patch -o ".$file_path.".patched");
    $patch_ok = (filesize($file_path) > 0);
    if ($patch_ok) {
        rename($file_path, $file_path.".bak_".date("Y-m-dTH:i:s"));
        rename($file_path.".patched", $file_path);
        file_put_contents(
            "{$results_data_path}_live.json",
            json_encode([
                'file' => $file_path,
                'last_updated_at' => date('Y-m-d H:i:s'),
            ]),
        );
    }
    die(json_encode([$patch_ok, $res]));
}
if (isset($_POST['new'])) {
    if (is_file($file_path)) {
        rename($file_path, $file_path.".bak_".date("Y-m-dTH:i:s"));
    }
    $new_content = base64_decode($_POST['new']);
    if (!$new_content) {
        die(json_encode([false, "Invalid base64 data"]));
    }
    file_put_contents($file_path, $new_content);
    file_put_contents(
        "{$results_data_path}_live.json",
        json_encode([
            'file' => $file_path,
            'last_updated_at' => date('Y-m-d H:i:s'),
        ]),
    );
    die(json_encode([true, ""]));
}
if ($file_path) {
    die("OK");
}
