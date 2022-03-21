<?php

require_once __DIR__.'/WithUtilsTrait.php';

class UploadUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'logger',
    ];

    /**
     * Kompatibilitäts-Layer, falls der Hoster eine bescheuerte Content Security
     * Policy haben sollte (hat er).
     */
    public function obfuscateForUpload(string $content) {
        $url_encoded_content = rawurlencode($content);
        $iv = floor(rand() * 0xFFFF / getrandmax());
        $upload_str = '';
        $current = $iv;
        for ($i = 0; $i < strlen($url_encoded_content); $i++) {
            $chr = ord(substr($url_encoded_content, $i, 1));
            $upload_str .= chr($chr ^ (($current >> 8) & 0xFF));
            $current = (($current << 5) - $current) & 0xFFFF;
        }
        $base64 = base64_encode($upload_str);
        return "{$iv};{$base64}";
    }

    /**
     * Kompatibilitäts-Layer, falls der Hoster eine bescheuerte Content Security
     * Policy haben sollte (hat er).
     */
    public function deobfuscateUpload(string $obfuscated) {
        $semipos = strpos($obfuscated, ';');
        $iv = intval(substr($obfuscated, 0, $semipos));
        $obfusbase64 = substr($obfuscated, $semipos + 1);
        $obfuscontent = base64_decode($obfusbase64);
        $url_encoded_content = '';
        $current = $iv;
        for ($i = 0; $i < strlen($obfuscontent); $i++) {
            $url_encoded_content .= chr(ord($obfuscontent[$i]) ^ (($current >> 8) & 0xFF));
            $current = (($current << 5) - $current) & 0xFFFF;
        }
        $content = rawurldecode($url_encoded_content);
        return $content;
    }

    public function getValidUploadIds($upload_ids) {
        $data_path = $this->envUtils->getDataPath();
        $valid_upload_ids = [];
        foreach ($upload_ids as $upload_id) {
            $upload_path = "{$data_path}temp/{$upload_id}";
            if (!is_file($upload_path)) {
                $this->logger->warning("Upload file {$upload_path} does not exist.");
                continue;
            }
            $valid_upload_ids[] = $upload_id;
        }
        return $valid_upload_ids;
    }

    public function moveUploads($upload_ids, $new_base_path) {
        if (!is_dir($new_base_path)) {
            mkdir($new_base_path, 0777, true);
        }
        $data_path = $this->envUtils->getDataPath();
        foreach ($upload_ids as $upload_id) {
            $upload_path = "{$data_path}temp/{$upload_id}";
            if (!is_file($upload_path)) {
                $this->logger->warning("Upload file {$upload_path} does not exist.");
                continue;
            }
            $destination_path = "{$new_base_path}{$upload_id}";
            rename($upload_path, $destination_path);
        }
    }
}
