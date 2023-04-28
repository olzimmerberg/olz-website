<?php

namespace Olz\Utils;

class MemorySession extends AbstractSession {
    public const UTILS = [];

    public $session_storage = [];
    public $cleared = false;

    public function resetConfigure($config) {
        $this->clear();
    }

    public function has($key) {
        return isset($this->session_storage[$key]);
    }

    public function get($key) {
        return $this->session_storage[$key] ?? null;
    }

    public function set($key, $new_value) {
        $this->session_storage[$key] = $new_value;
    }

    public function delete($key) {
        unset($this->session_storage[$key]);
    }

    public function clear() {
        $this->session_storage = [];
        $this->cleared = true;
    }
}
