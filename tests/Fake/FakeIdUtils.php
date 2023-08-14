<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\IdUtils;

class FakeIdUtils extends IdUtils {
    public function toExternalId($internal_id, $type = '') {
        return "{$type}:{$internal_id}";
    }

    public function toInternalId($external_id, $type = '') {
        $res = preg_match('/^(.+):([0-9]+)$/', $external_id, $matches);
        if (!$res || $matches[1] !== $type) {
            throw new \Exception("Invalid serialized ID: Type mismatch");
        }
        return intval($matches[2]);
    }
}
