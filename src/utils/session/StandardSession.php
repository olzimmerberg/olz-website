<?php

require_once __DIR__.'/../../config/init.php';
require_once __DIR__.'/AbstractSession.php';

class StandardSession extends AbstractSession {
    public function __construct() {
        try {
            $was_successful = session_start();
        } catch (Exception $exc) {
            $was_successful = false;
        }
        if (!$was_successful) {
            throw new Exception("Could not create session.");
        }
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function get($key) {
        return $_SESSION[$key];
    }

    public function set($key, $new_value) {
        $_SESSION[$key] = $new_value;
    }

    public function delete($key) {
        unset($_SESSION[$key]);
    }
}
