<?php

use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

require_once __DIR__.'/api.php';
require_once __DIR__.'/HttpError.php';
require_once __DIR__.'/validate.php';
require_once __DIR__.'/../../config/vendor/autoload.php';
require_once __DIR__.'/../../utils/session/AbstractSession.php';

abstract class Endpoint {
    use Psr\Log\LoggerAwareTrait;

    protected ?AbstractSession $session = null;
    private $setupFunction;

    public function setup() {
        $setup_function = $this->setupFunction;
        if ($setup_function == null) {
            throw new Exception("Setup function must be set");
        }
        $setup_function($this);
    }

    abstract public static function getIdent();

    public function setDefaultFileLogger() {
        $logger = new Logger($this->getIdent());
        $logger->pushHandler(new ErrorLogHandler());
        $this->setLogger($logger);
    }

    public function setSetupFunction($new_setup_function) {
        $this->setupFunction = $new_setup_function;
    }

    public function call($raw_input) {
        try {
            $validated_input = backend_validate($this->getRequestFields(), $raw_input);
        } catch (ValidationError $verr) {
            $this->logger->warning("Bad user request", $verr->getStructuredAnswer());
            throw new HttpError(400, "Fehlerhafte Eingabe.", $verr);
        }

        try {
            $raw_result = $this->handle($validated_input);
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->logger->critical("Unexpected endpoint error: {$message}", $exc->getTrace());
            throw new HttpError(500, "Es ist ein Fehler aufgetreten. Bitte später nochmals versuchen.");
        }

        try {
            $validated_result = backend_validate($this->getResponseFields(), $raw_result);
        } catch (ValidationError $verr) {
            $this->logger->critical("Bad output prohibited", $verr->getStructuredAnswer());
            throw new HttpError(500, "Es ist ein Fehler aufgetreten. Bitte später nochmals versuchen.");
        }

        return $validated_result;
    }

    abstract public function getRequestFields();

    abstract public function getResponseFields();

    public function setSession(AbstractSession $new_session) {
        $this->session = $new_session;
    }

    public function setServer($new_server) {
        $this->server = $new_server;
    }

    abstract protected function handle($input);
}
