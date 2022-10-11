<?php

namespace Olz\Utils;

class UploadUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'generalUtils',
        'log',
    ];

    private $suffixPattern = '[a-z0-9]+';

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

    public function isUploadId($potential_upload_id) {
        if (!is_string($potential_upload_id)) {
            return false;
        }
        return (bool) preg_match(
            "/^[a-zA-Z0-9_-]{24}\\.{$this->suffixPattern}$/",
            $potential_upload_id
        );
    }

    public function getRandomUploadId($suffix) {
        if (!preg_match("/^\\.{$this->suffixPattern}$/", $suffix)) {
            throw new \Exception("Invalid upload ID suffix: {$suffix}");
        }
        $random_id = $this->generalUtils()->base64EncodeUrl(openssl_random_pseudo_bytes(18));
        return "{$random_id}{$suffix}";
    }

    public function getValidUploadIds($upload_ids) {
        $valid_upload_ids = [];
        foreach ($upload_ids as $upload_id) {
            $upload_id_or_null = $this->getValidUploadId($upload_id);
            if ($upload_id_or_null !== null) {
                $valid_upload_ids[] = $upload_id;
            }
        }
        return $valid_upload_ids;
    }

    public function getValidUploadId($upload_id) {
        if (!$this->isUploadId($upload_id)) {
            $this->log()->warning("Upload ID {$upload_id} is invalid.");
            return null;
        }
        $data_path = $this->envUtils()->getDataPath();
        $upload_path = "{$data_path}temp/{$upload_id}";
        if (!is_file($upload_path)) {
            $this->log()->warning("Upload file {$upload_path} does not exist.");
            return null;
        }
        return $upload_id;
    }

    public function getStoredUploadIds($base_path) {
        $stored_upload_ids = [];
        $entries = scandir($base_path);
        foreach ($entries as $upload_id) {
            if ($this->isUploadId($upload_id)) {
                $stored_upload_ids[] = $upload_id;
            }
        }
        return $stored_upload_ids;
    }

    public function moveUploads($upload_ids, $new_base_path) {
        if (!is_dir($new_base_path)) {
            mkdir($new_base_path, 0777, true);
        }
        $data_path = $this->envUtils()->getDataPath();
        foreach ($upload_ids as $upload_id) {
            if (!$this->isUploadId($upload_id)) {
                $this->log()->warning("Upload ID {$upload_id} is invalid.");
                continue;
            }
            $upload_path = "{$data_path}temp/{$upload_id}";
            if (!is_file($upload_path)) {
                $this->log()->warning("Upload file {$upload_path} does not exist.");
                continue;
            }
            $destination_path = "{$new_base_path}{$upload_id}";
            rename($upload_path, $destination_path);
        }
    }
}
