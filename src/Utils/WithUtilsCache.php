<?php

namespace Olz\Utils;

class WithUtilsCache {
    private static $utilsCache = [];

    public static function getAll() {
        return [...self::$utilsCache];
    }

    public static function setAll($utils) {
        self::$utilsCache = $utils;
    }

    public static function get($name) {
        return self::$utilsCache[$name] ?? null;
    }

    public static function set($name, $util) {
        self::$utilsCache[$name] = $util;
    }

    public static function reset() {
        self::$utilsCache = [];
    }
}
