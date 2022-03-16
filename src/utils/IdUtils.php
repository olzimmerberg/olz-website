<?php

require_once __DIR__.'/GeneralUtils.php';

class IdUtils {
    public function toExternalId($internal_id, $type = '') {
        $int_internal_id = intval($internal_id);
        if (strval($int_internal_id) !== strval($internal_id)) {
            throw new Exception("Internal ID must be int");
        }
        $type_hash = $this->trimmedBase64Encode(hex2bin(hash('crc32', $type)));
        return $this->trimmedBase64Encode("{$int_internal_id}-{$type_hash}");
    }

    public function toInternalId($external_id, $type = '') {
        $decoded = base64_decode($external_id);
        $type_hash = $this->trimmedBase64Encode(hex2bin(hash('crc32', $type)));
        $res = preg_match('/^([0-9]+)\-([a-zA-Z0-9\+\/]{6})$/', $decoded, $matches);
        if (!$res) {
            throw new Exception("Invalid external ID: No match");
        }
        $internal_id = intval($matches[1]);
        if (!$internal_id) {
            throw new Exception("Invalid external ID: Falsy ID");
        }
        if ($matches[2] !== $type_hash) {
            throw new Exception("Invalid external ID: Type mismatch");
        }
        return $internal_id;
    }

    private function trimmedBase64Encode($string) {
        return trim(base64_encode($string), '=');
    }

    public static function fromEnv() {
        return new self();
    }
}
