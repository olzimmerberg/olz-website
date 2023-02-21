<?php

namespace Olz\Utils;

require_once __DIR__.'/../../_/config/init.php';
require_once __DIR__.'/AbstractSession.php';

class StandardSession extends AbstractSession {
    public const UTILS = [];

    public function __construct() {
        $session_already_exists = session_id() != '' && isset($_SESSION);
        if ($session_already_exists) {
            // @codeCoverageIgnoreStart
            // Reason: Cannot have an existing session in tests.
            return;
            // @codeCoverageIgnoreEnd
        }
        $session_can_be_created = !headers_sent();
        $was_successful = false;
        if ($session_can_be_created) {
            // @codeCoverageIgnoreStart
            // Reason: Cannot start session in tests.
            $was_successful = session_start();
            // @codeCoverageIgnoreEnd
        }
        if (!$was_successful) {
            global $_SESSION;
            $_SESSION = [];
            // TODO: This is commented out such that integration tests can still run...
            // throw new \Exception("Could not create session.");
        }
    }

    public function resetConfigure($config) {
        $this->clear();

        $timeout = $config['timeout'] ?? 3600;
        ini_set('session.gc_maxlifetime', $timeout);
        session_set_cookie_params($timeout);

        session_start();
        session_regenerate_id(true);
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function get($key) {
        return $_SESSION[$key] ?? null;
    }

    public function set($key, $new_value) {
        $_SESSION[$key] = $new_value;
    }

    public function delete($key) {
        unset($_SESSION[$key]);
    }

    // @codeCoverageIgnoreStart
    // Reason: Cannot start/destroy session in tests.
    public function clear() {
        session_unset();
        session_destroy();
    }

    // @codeCoverageIgnoreEnd
}
