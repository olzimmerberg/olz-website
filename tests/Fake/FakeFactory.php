<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeFactory {
    public static $cache = [];

    protected static function getFake(
        string $ident,
        bool $should_generate_new,
        callable $generate_new
    ) {
        $called_class = get_called_class();
        $namespaced_ident = "{$called_class}::{$ident}";
        if ($should_generate_new) {
            return $generate_new();
        }
        $cached = self::$cache[$namespaced_ident] ?? null;
        if ($cached) {
            return $cached;
        }
        $fake = $generate_new();
        self::$cache[$namespaced_ident] = $fake;
        return $fake;
    }

    public static function reset() {
        self::$cache = [];
    }
}
