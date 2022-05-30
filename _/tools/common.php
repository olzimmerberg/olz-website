<?php

function remove_r($path) {
    if (is_dir($path)) {
        $entries = scandir($path);
        foreach ($entries as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                $entry_path = realpath("{$path}/{$entry}");
                remove_r($entry_path);
            }
        }
        rmdir($path);
    } elseif (is_file($path)) {
        unlink($path);
    }
}
