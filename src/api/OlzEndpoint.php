<?php

use PhpTypeScriptApi\Endpoint;

abstract class OlzEndpoint extends Endpoint {
    use \Psr\Log\LoggerAwareTrait;

    public function runtimeSetup() {
        require_once __DIR__.'/../utils/session/StandardSession.php';
        require_once __DIR__.'/../utils/env/EnvUtils.php';
        $env_utils = EnvUtils::fromEnv();
        $logger_name = "Endpoint:{$this->getIdent()}";
        $logger = $env_utils->getLogsUtils()->getLogger($logger_name);
        $session = new StandardSession();
        $this->setLogger($logger);
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
