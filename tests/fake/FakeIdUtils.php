<?php

declare(strict_types=1);

require_once __DIR__.'/../../public/_/config/vendor/autoload.php';

class FakeIdUtils {
    public function toExternalId($internal_id, $type = '') {
        return "{$type}:{$internal_id}";
    }

    public function toInternalId($external_id, $type = '') {
        $res = preg_match('/^(.+):([0-9]+)$/', $external_id, $matches);
        if (!$res || $matches[1] !== $type) {
            throw new Exception("Invalid serialized ID: Type mismatch");
        }
        return intval($matches[2]);
    }
}
