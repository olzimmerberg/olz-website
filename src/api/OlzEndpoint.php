<?php

use PhpTypeScriptApi\Endpoint;

abstract class OlzEndpoint extends Endpoint {
    public function runtimeSetup() {
        require_once __DIR__.'/../utils/session/StandardSession.php';
        $session = new StandardSession();
        $this->setServer($_SERVER);
        $this->setSession($session);
    }

    public function setSession(AbstractSession $new_session) {
        $this->session = $new_session;
    }

    public function setServer($new_server) {
        $this->server = $new_server;
    }
}
