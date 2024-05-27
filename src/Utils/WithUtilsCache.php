<?php

namespace Olz\Utils;

class WithUtilsCache {
    private static array $utilsCache = [];

    public static function getAll(): array {
        return [...self::$utilsCache];
    }

    public static function setAll(array $utils): void {
        self::$utilsCache = $utils;
    }

    public static function get(string $name): mixed {
        return self::$utilsCache[$name] ?? null;
    }

    public static function set(string $name, mixed $util): void {
        self::$utilsCache[$name] = $util;
    }

    public static function reset(): void {
        self::$utilsCache = [];
    }
}
