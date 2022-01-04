<?php

require_once __DIR__.'/../Deploy.php';

class ScreenshotUpload extends Deploy {
    public function getPublicFlysystemFilesystem() {
        return $this->getFlysystemFilesystem();
    }

    public function __construct() {
        $opts = $this->getCommandLineOptions();
        $environment = $opts['environment'];
        $username = $opts['username'];
        $password = $this->getEnvironmentVariable('PASSWORD');
        $this->environment = $environment;
        $this->username = $username;
        $this->password = $password;
    }
}

$upload = new ScreenshotUpload();
$remote_fs = $upload->getPublicFlysystemFilesystem();

$remote_screenshots_path = 'public_html/deploy/live/screenshots/generated';
try {
    $remote_fs->createDirectory(dirname($remote_screenshots_path));
} catch (\Throwable $th) {
    // ignore
}
try {
    $remote_fs->createDirectory($remote_screenshots_path);
} catch (\Throwable $th) {
    // ignore
}
$screenshots_dir = __DIR__.'/../screenshots';
foreach (scandir($screenshots_dir) as $entry) {
    $file_path = "{$screenshots_dir}/{$entry}";
    if (is_file($file_path)) {
        $remote_file_path = "{$remote_screenshots_path}/{$entry}";
        $file_contents = file_get_contents($file_path);
        $remote_fs->write($remote_file_path, $file_contents);
    }
}
