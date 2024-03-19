<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

class FakeEntity {
    public static $cache = [];

    protected static function getFake(
        bool $should_generate_new,
        callable $generate_new
    ) {
        $trace = debug_backtrace(0, 2);
        $class = $trace[1]['class'];
        $function = $trace[1]['function'];
        $args = json_encode($trace[1]['args']);
        $ident = "{$class}::{$function}({$args})";
        if ($should_generate_new) {
            return $generate_new();
        }
        $cached = self::$cache[$ident] ?? null;
        if ($cached) {
            return $cached;
        }
        $fake = $generate_new();
        self::$cache[$ident] = $fake;
        return $fake;
    }

    public static function reset() {
        self::$cache = [];
    }
}
