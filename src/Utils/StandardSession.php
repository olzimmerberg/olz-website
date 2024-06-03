<?php

namespace Olz\Utils;

class StandardSession extends AbstractSession {
    use WithUtilsTrait;

    public function __construct() {
        self::session_start_if_cookie_set();
    }

    /** @param array{timeout?: int} $config */
    public function resetConfigure(array $config): void {
        global $_SESSION;
        $session_already_exists = session_id() != '' && isset($_SESSION);
        if ($session_already_exists) {
            $this->clear();
        }

        $timeout = $config['timeout'] ?? 3600;
        ini_set('session.gc_maxlifetime', $timeout);
        session_set_cookie_params($timeout);

        $session_can_be_created = !headers_sent();
        $was_successful = false;
        if ($session_can_be_created) {
            // @codeCoverageIgnoreStart
            // Reason: Cannot start session in tests.
            // $was_successful = session_start();
            $was_successful = session_start() && session_regenerate_id(true);
            // @codeCoverageIgnoreEnd
        }
    }

    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public function get(string $key): ?string {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, ?string $new_value): void {
        $_SESSION[$key] = $new_value;
    }

    public function delete(string $key): void {
        unset($_SESSION[$key]);
    }

    // @codeCoverageIgnoreStart
    // Reason: Cannot start/destroy session in tests.

    public function clear(): void {
        @session_unset();
        @session_destroy();
        @setcookie(session_name(), '', time() - 3600, '/');
    }

    public static function session_start_if_cookie_set(): void {
        if (isset($_COOKIE[session_name()])) {
            @session_start();
        }
    }

    // @codeCoverageIgnoreEnd
}
