<?php

namespace Olz\Utils;

abstract class AbstractSession {
    /** @param array{timeout?: int} $config */
    abstract public function resetConfigure(array $config): void;

    abstract public function has(string $key): bool;

    abstract public function get(string $key): ?string;

    abstract public function set(string $key, ?string $new_value): void;

    abstract public function delete(string $key): void;

    abstract public function clear(): void;
}
