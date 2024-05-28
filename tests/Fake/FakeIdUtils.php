<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\IdUtils;

class FakeIdUtils extends IdUtils {
    public function toExternalId(int|string $internal_id, string $type = ''): string {
        return "{$type}:{$internal_id}";
    }

    public function toInternalId(string $external_id, string $type = ''): int {
        $res = preg_match('/^(.+):([0-9]+)$/', $external_id, $matches);
        if (!$res || $matches[1] !== $type) {
            throw new \Exception("Invalid serialized ID: Type mismatch");
        }
        return intval($matches[2]);
    }
}
