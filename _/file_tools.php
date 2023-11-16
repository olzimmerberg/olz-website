<?php

// =============================================================================
// Funktionen für Datei-Upload, z.B. PDFs in News-Einträgen.
// =============================================================================

use Olz\Utils\EnvUtils;
use Olz\Utils\FileUtils;

require_once __DIR__.'/config/init.php';

global $mime_extensions, $extension_icons;

$mime_extensions = FileUtils::MIME_EXTENSIONS;
$extension_icons = FileUtils::EXTENSION_ICONS;

if (basename($_SERVER["SCRIPT_FILENAME"] ?? '') == basename(__FILE__)) {
    if (!isset($_GET["request"])) {
        header("Content-type:text/plain");
        echo "HIER IST NIX";
    }

    if ($_GET["request"] == "thumb") {
        $data_path = EnvUtils::fromEnv()->getDataPath();

        session_write_close();
        $db_table = $_GET["db_table"];
        if (!isset(FileUtils::TABLES_FILE_DIRS[$db_table])) {
            echo "Invalid db_table (in thumb)";
            return;
        }
        $db_filepath = FileUtils::TABLES_FILE_DIRS[$db_table];
        $id = intval($_GET["id"]);
        if ($id <= 0) {
            echo "Invalid id (in thumb)";
            return;
        }
        $index = $_GET["index"];
        $is_migrated = !(is_numeric($index) && intval($index) > 0 && intval($index) == $index);
        if ($is_migrated) {
            if (!preg_match("/^[0-9A-Za-z_\\-]{24}\\.\\S{1,10}$/", $index)) {
                echo "Invalid index (=hash; in thumb)";
                return;
            }
        } else {
            if ($index <= 0) {
                echo "Invalid index (in thumb)";
                return;
            }
        }
        $dim = 16;
        if (intval($_GET["dim"]) > 16) {
            $dim = 128;
        }
        if ($is_migrated) {
            preg_match("/^[0-9A-Za-z_\\-]{24}\\.(\\S{1,10})$/", $index, $matches);
            $thumbfile = __DIR__."/../assets/icns/link_".$extension_icons[$matches[1]]."_16.svg";
            if (!is_file($thumbfile)) {
                $thumbfile = __DIR__."/../assets/icns/link_any_16.svg";
            }
            header("Cache-Control: max-age=86400");
            header("Content-Type: image/svg+xml");
            $fp = fopen($thumbfile, "r");
            $buf = fread($fp, 1024);
            while ($buf) {
                echo $buf;
                $buf = fread($fp, 1024);
            }
            fclose($fp);
        } else {
            $files = scandir($data_path.$db_filepath."/".$id);
            for ($i = 0; $i < count($files); $i++) {
                if (preg_match("/^([0-9]{3})\\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                    if (intval($matches[1]) == $index) {
                        $thumbfile = __DIR__."/../assets/icns/link_".$extension_icons[$matches[2]]."_16.svg";
                        if (!is_file($thumbfile)) {
                            $thumbfile = __DIR__."/../assets/icns/link_any_16.svg";
                        }
                        header("Cache-Control: max-age=86400");
                        header("Content-Type: image/svg+xml");
                        $fp = fopen($thumbfile, "r");
                        $buf = fread($fp, 1024);
                        while ($buf) {
                            echo $buf;
                            $buf = fread($fp, 1024);
                        }
                        fclose($fp);
                    }
                }
            }
        }
    }
}
