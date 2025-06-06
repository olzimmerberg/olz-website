<?php

namespace Olz\Tests\Fake;

use Olz\Utils\Session;

class FakeSession extends Session {
    /** @var array<string, ?string> */
    public array $session_storage = [];
    public bool $cleared = false;

    /** @param array{timeout?: int} $config */
    public function resetConfigure(array $config): void {
        $this->clear();
    }

    public function has(string $key): bool {
        return isset($this->session_storage[$key]);
    }

    public function get(string $key): ?string {
        return $this->session_storage[$key] ?? null;
    }

    public function set(string $key, ?string $new_value): void {
        $this->session_storage[$key] = $new_value;
    }

    public function delete(string $key): void {
        unset($this->session_storage[$key]);
    }

    public function clear(): void {
        $this->session_storage = [];
        $this->cleared = true;
    }
}
