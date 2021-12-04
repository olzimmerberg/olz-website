<?php

class Session {
    public static function fromEnv() {
        require_once __DIR__.'/StandardSession.php';

        return new StandardSession();
    }
}
