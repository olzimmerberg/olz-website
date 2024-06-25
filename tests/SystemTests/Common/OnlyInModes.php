<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests\Common;

#[\Attribute(\Attribute::TARGET_METHOD)]
class OnlyInModes {
    /** @param array<string>|string $modes */
    public function __construct(
        public array|string $modes,
    ) {
    }
}
