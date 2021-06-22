<?php
/**
 * Deploy code on our hoster.
 *
 * Premise: CI has connected to the hoster and uploaded this file and
 * `deploy.zip` (containing all code) to the deploy directory (the directory
 * the CI has access to) and has started an HTTP connection to invoke this file.
 */

// Constants
$date = date('Y-m-d_H_i_s');
$root_path = $_SERVER['DOCUMENT_ROOT'];
$php_path = './deploy.php';
$zip_path = './deploy.zip';
$unzip_path = './unzip/';
$current_deployment_unzip_path = "{$unzip_path}deploy/";
$current_deployment_destination_path = "./{$date}";
$current_link_path = './current';

// Unzip the uploaded file with all the code to be deployed.
$zip = new ZipArchive();
$res = $zip->open($zip_path);
if (!$res) {
    // Keep the zip (for debugging purposes).
    rename($zip_path, "./invalid_deploy_{$date}.zip");
    unlink($php_path);
    http_response_code(500);
    exit("Could not unzip deploy.zip\n");
}
mkdir($unzip_path, 0777, true);
$zip->extractTo($unzip_path);
$zip->close();
unlink($zip_path);

// Quickly verify the unzipped content.
if (!is_dir($current_deployment_unzip_path)) {
    // Keep the unzipped directory (for debugging purposes).
    rename($unzip_path, "./invalid_unzip_{$date}");
    unlink($php_path);
    http_response_code(500);
    exit("Invalid zip content: unzip/deploy/ not found\n");
}

// Move the code to the appropriate destination.
rename(
    $current_deployment_unzip_path,
    $current_deployment_destination_path,
);

// Re-create root symlinks
@unlink("{$root_path}/_");
symlink(realpath($current_link_path), "{$root_path}/_");
@unlink("{$root_path}/robots.txt");
symlink("{$root_path}/_/robots.txt", "{$root_path}/robots.txt");
@unlink("{$root_path}/manifest.json");
symlink("{$root_path}/_/pwa/manifest.json", "{$root_path}/manifest.json");
@unlink("{$root_path}/pwa-service-worker.js");
symlink("{$root_path}/_/pwa/jsbuild/service-worker.min.js", "{$root_path}/pwa-service-worker.js");

// Run database migrations
// TODO: Could not be implemented as of 2020-04-11
$doctrine_migrations_lib_path = "{$current_deployment_destination_path}/tools/doctrine_migrations.php";
if (is_file($doctrine_migrations_lib_path)) {
    require_once $doctrine_migrations_lib_path;
    migrate_to_latest();
    echo "SUCCESSFULLY MIGRATED\n";
} else {
    echo "NOT MIGRATED\n";
}

// Redirect users to the new code.
unlink($current_link_path);
symlink($current_deployment_destination_path, $current_link_path);

// Clean up.
rmdir($unzip_path);
unlink($php_path);

echo "deploy:SUCCESS";
