<?php

// =============================================================================
// Funktionen für Datei-Upload, z.B. PDFs in News-Einträgen.
// =============================================================================

use Olz\Utils\FileUtils;
use Olz\Utils\LogsUtils;

require_once __DIR__.'/config/init.php';

global $mime_extensions, $extension_icons;

$mime_extensions = FileUtils::MIME_EXTENSIONS;
$extension_icons = FileUtils::EXTENSION_ICONS;

if (basename($_SERVER["SCRIPT_FILENAME"] ?? '') == basename(__FILE__)) {
    if (!isset($_GET["request"])) {
        header("Content-type:text/plain");
        echo "HIER IST NIX";
    }

    $request = $_GET["request"];
    LogsUtils::fromEnv()->notice("Remaining use of file_tools.php (request: {$request})");
}
