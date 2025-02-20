<?php

namespace Olz\Utils;

class IdUtils {
    use WithUtilsTrait;

    protected string $base64Iv = '9V0IXtcQo5o=';
    protected string $algo = 'des-ede-cbc'; // Find one using `composer get_id_algos`

    public function toExternalId(int $internal_id, string $type = ''): string {
        $serialized_id = $this->serializeId($internal_id, $type);
        return $this->encryptId($serialized_id);
    }

    protected function serializeId(int $internal_id, string $type): string {
        if ($internal_id < 0) {
            throw new \Exception("Internal ID must be positive");
        }
        $type_hash_hex = str_pad(dechex($this->crc16($type)), 4, '0', STR_PAD_LEFT);
        $id_hex = str_pad(dechex($internal_id), 10, '0', STR_PAD_LEFT);
        if (strlen($id_hex) > 10) {
            throw new \Exception("Internal ID must be at most 40 bits");
        }
        $type_id_hex = "{$type_hash_hex}{$id_hex}";
        $type_id_bin = hex2bin($type_id_hex);
        if (!$type_id_bin) {
            throw new \Exception("hex2bin({$type_id_hex}) failed");
        }
        return $type_id_bin;
    }

    protected function encryptId(string $serialized_id): string {
        $plaintext = $serialized_id;
        $key = $this->envUtils()->getIdEncryptionKey();
        $iv = base64_decode($this->base64Iv);
        $ciphertext = @openssl_encrypt($plaintext, $this->algo, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            $error_string = openssl_error_string();
            throw new \Exception("{$error_string}");
        }
        return $this->generalUtils()->base64EncodeUrl($ciphertext);
    }

    public function toInternalId(string $external_id, string $type = ''): int {
        $serialized_id = $this->decryptId($external_id);
        return $this->deserializeId($serialized_id, $type);
    }

    protected function decryptId(string $encrypted_id): ?string {
        $ciphertext = $this->generalUtils()->base64DecodeUrl($encrypted_id);
        $key = $this->envUtils()->getIdEncryptionKey();
        $iv = base64_decode($this->base64Iv);
        $plaintext = openssl_decrypt($ciphertext, $this->algo, $key, OPENSSL_RAW_DATA, $iv);
        if (!$plaintext) {
            throw new \Exception("Could not decrypt ID: {$encrypted_id}");
        }
        return $plaintext;
    }

    protected function deserializeId(string $serialized_id, string $type): int {
        $expected_type_hash_hex = str_pad(dechex($this->crc16($type)), 4, '0', STR_PAD_LEFT);
        $serialized_id_hex = bin2hex($serialized_id);
        $actual_type_hash_hex = substr($serialized_id_hex, 0, 4);
        if ($actual_type_hash_hex !== $expected_type_hash_hex) {
            throw new \Exception("Invalid serialized ID: Type mismatch {$actual_type_hash_hex} vs. {$expected_type_hash_hex}");
        }
        return intval(hexdec(substr($serialized_id_hex, 4)));
    }

    protected function crc16(string $data): int {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
