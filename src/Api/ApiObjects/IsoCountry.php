<?php

namespace Olz\Api\ApiObjects;

use PhpTypeScriptApi\PhpStan\ApiObjectInterface;

/**
 * @implements ApiObjectInterface<non-empty-string>
 */
class IsoCountry implements ApiObjectInterface {
    /** @param non-empty-string $alpha2 */
    public function __construct(protected string $alpha2) {
    }

    public function data(): mixed {
        return $this->alpha2;
    }

    public static function fromData(mixed $data): IsoCountry {
        if (!is_string($data)) {
            throw new \InvalidArgumentException("IsoCountry must be string");
        }
        if (!preg_match('/^[a-zA-Z]{2}$/', $data)) {
            throw new \InvalidArgumentException("IsoCountry must be a 2-letter code");
        }
        return new IsoCountry($data);
    }
}
