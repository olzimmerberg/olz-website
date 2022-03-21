<?php

use PhpTypeScriptApi\Endpoint;

require_once __DIR__.'/../utils/WithUtilsTrait.php';

abstract class OlzEndpoint extends Endpoint {
    use \Psr\Log\LoggerAwareTrait;
    use WithUtilsTrait;

    public function runtimeSetup() {
        $this->populateFromEnv();
    }

    public function setSession(AbstractSession $new_session) {
        $this->session = $new_session;
    }

    public function setServer($new_server) {
        $this->server = $new_server;
    }
}
