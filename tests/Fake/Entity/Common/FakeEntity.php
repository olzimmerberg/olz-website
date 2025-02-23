<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

/**
 * @template T of object
 */
class FakeEntity {
    /** @var array<string, T> */
    public static array $cache = [];

    /** @return T */
    protected static function getFake(
        bool $should_generate_new,
        callable $generate_new,
        ?callable $populate_new = null,
    ): object {
        $trace = debug_backtrace(0, 2);
        $class = $trace[1]['class'] ?? '(no class)';
        $function = $trace[1]['function'];
        $args = json_encode($trace[1]['args'] ?? null);
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
        if ($populate_new) {
            $populate_new($fake);
        }
        return $fake;
    }

    public static function reset(): void {
        self::$cache = [];
    }
}
