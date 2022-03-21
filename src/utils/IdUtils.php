<?php

require_once __DIR__.'/WithUtilsTrait.php';

class IdUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
    ];

    protected const BASE64_IV = '9V0IXtcQo5o=';
    protected const ALGO = 'bf-cbc';

    public function toExternalId($internal_id, $type = '') {
        $serialized_id = $this->serializeId($internal_id, $type);
        return $this->encryptId($serialized_id);
    }

    protected function serializeId($internal_id, $type) {
        $int_internal_id = intval($internal_id);
        if (strval($int_internal_id) !== strval($internal_id)) {
            throw new Exception("Internal ID must be int");
        }
        if ($int_internal_id < 0) {
            throw new Exception("Internal ID must be positive");
        }
        $type_hash_hex = str_pad(dechex($this->crc16($type)), 4, '0', STR_PAD_LEFT);
        $id_hex = str_pad(dechex($int_internal_id), 10, '0', STR_PAD_LEFT);
        if (strlen($id_hex) > 10) {
            throw new Exception("Internal ID must be at most 40 bits");
        }
        return hex2bin($type_hash_hex.$id_hex);
    }

    protected function encryptId($serialized_id) {
        $plaintext = $serialized_id;
        $algo = self::ALGO;
        $key = $this->envUtils->getIdEncryptionKey();
        $iv = base64_decode(self::BASE64_IV);
        $ciphertext = openssl_encrypt($plaintext, $algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $this->trimmedBase64Encode($ciphertext);
    }

    public function toInternalId($external_id, $type = '') {
        $serialized_id = $this->decryptId($external_id);
        return $this->deserializeId($serialized_id, $type);
    }

    protected function decryptId($encrypted_id) {
        $ciphertext = base64_decode($encrypted_id);
        $algo = self::ALGO;
        $key = $this->envUtils->getIdEncryptionKey();
        $iv = base64_decode(self::BASE64_IV);
        return openssl_decrypt($ciphertext, $algo, $key, OPENSSL_RAW_DATA, $iv);
    }

    protected function deserializeId($serialized_id, $type) {
        $type_hash_hex = str_pad(dechex($this->crc16($type)), 4, '0', STR_PAD_LEFT);
        $serialized_id_hex = bin2hex($serialized_id);
        if (substr($serialized_id_hex, 0, 4) !== $type_hash_hex) {
            throw new Exception("Invalid serialized ID: Type mismatch");
        }
        return hexdec(substr($serialized_id_hex, 4));
    }

    protected function crc16($data) {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }

    protected function trimmedBase64Encode($data) {
        return trim(base64_encode($data), '=');
    }
}
