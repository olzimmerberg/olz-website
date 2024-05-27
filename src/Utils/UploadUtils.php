<?php

namespace Olz\Utils;

class UploadUtils {
    use WithUtilsTrait;

    private string $suffixPattern = '[a-zA-Z0-9]+';

    /**
     * Kompatibilitäts-Layer, falls der Hoster eine bescheuerte Content Security
     * Policy haben sollte (hat er).
     */
    public function obfuscateForUpload(string $content): string {
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
    public function deobfuscateUpload(string $obfuscated): string {
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

    public function isUploadId(mixed $potential_upload_id): bool {
        if (!is_string($potential_upload_id)) {
            return false;
        }
        return (bool) preg_match(
            "/^{$this->getUploadIdRegex()}$/",
            $potential_upload_id
        );
    }

    public function getExtension(string $upload_id): ?string {
        $is_match = preg_match("/^{$this->getUploadIdRegex()}$/", $upload_id, $matches);
        return $is_match ? $matches[2] : null;
    }

    public function getUploadIdRegex(): string {
        return "([a-zA-Z0-9_-]{24})(\\.{$this->suffixPattern})";
    }

    public function getRandomUploadId(string $suffix): string {
        if (!preg_match("/^\\.{$this->suffixPattern}$/", $suffix)) {
            throw new \Exception("Invalid upload ID suffix: {$suffix}");
        }
        $random_id = $this->generalUtils()->base64EncodeUrl(openssl_random_pseudo_bytes(18));
        return "{$random_id}{$suffix}";
    }

    public function getValidUploadIds(?array $upload_ids): array {
        $valid_upload_ids = [];
        foreach ($upload_ids ?? [] as $upload_id) {
            $upload_id_or_null = $this->getValidUploadId($upload_id);
            if ($upload_id_or_null !== null) {
                $valid_upload_ids[] = $upload_id;
            }
        }
        return $valid_upload_ids;
    }

    public function getValidUploadId(string $upload_id): ?string {
        if (!$this->isUploadId($upload_id)) {
            $this->log()->warning("Upload ID \"{$upload_id}\" is invalid.");
            return null;
        }
        $data_path = $this->envUtils()->getDataPath();
        $upload_path = "{$data_path}temp/{$upload_id}";
        if (!is_file($upload_path)) {
            $this->log()->warning("Upload file \"{$upload_path}\" does not exist.");
            return null;
        }
        return $upload_id;
    }

    public function getStoredUploadIds(string $base_path): array {
        $stored_upload_ids = [];
        if (!is_dir($base_path)) {
            return [];
        }
        $entries = scandir($base_path);
        foreach ($entries as $upload_id) {
            if ($this->isUploadId($upload_id)) {
                $stored_upload_ids[] = $upload_id;
            }
        }
        return $stored_upload_ids;
    }

    public function overwriteUploads(?array $upload_ids, string $new_base_path): void {
        if (!is_dir($new_base_path)) {
            mkdir($new_base_path, 0o777, true);
        }
        $existing_file_names = scandir($new_base_path);
        foreach ($existing_file_names as $file_name) {
            if (substr($file_name, 0, 1) !== '.') {
                $file_path = "{$new_base_path}{$file_name}";
                if (is_file($file_path)) {
                    $this->log()->info("Deleting existing upload: {$file_path}.");
                    unlink($file_path);
                } else {
                    // @codeCoverageIgnoreStart
                    // Reason: Hard to test
                    $this->log()->notice("Cannot delete existing upload: {$file_path}.");
                    // @codeCoverageIgnoreEnd
                }
            }
        }
        $data_path = $this->envUtils()->getDataPath();
        foreach ($upload_ids ?? [] as $upload_id) {
            if (!$this->isUploadId($upload_id)) {
                $this->log()->warning("Upload ID \"{$upload_id}\" is invalid.");
                continue;
            }
            $upload_path = "{$data_path}temp/{$upload_id}";
            if (!is_file($upload_path)) {
                $this->log()->warning("Upload file \"{$upload_path}\" does not exist.");
                continue;
            }
            $destination_path = "{$new_base_path}{$upload_id}";
            rename($upload_path, $destination_path);
        }
    }

    public function editUploads(?array $upload_ids, string $base_path): void {
        $data_path = $this->envUtils()->getDataPath();
        foreach ($upload_ids ?? [] as $upload_id) {
            if (!$this->isUploadId($upload_id)) {
                $this->log()->warning("Upload ID \"{$upload_id}\" is invalid.");
                continue;
            }
            $storage_path = "{$base_path}{$upload_id}";
            if (!is_file($storage_path)) {
                $this->log()->warning("Storage file \"{$storage_path}\" does not exist.");
                continue;
            }
            $temp_path = "{$data_path}temp/{$upload_id}";
            copy($storage_path, $temp_path);
        }
    }

    public static function fromEnv(): self {
        return new self();
    }
}
