<?php

use Monolog\Handler\RotatingFileHandler;
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

    /** Override to enjoy throttling! */
    public function shouldFailThrottling() {
        return false;
    }

    public function setDefaultFileLogger() {
        global $data_path;
        require_once __DIR__.'/../../config/paths.php';
        $log_path = "{$data_path}logs/";
        if (!is_dir(dirname($log_path))) {
            mkdir(dirname($log_path), 0777, true);
        }
        $logger = new Logger($this->getIdent());
        $logger->pushHandler(new RotatingFileHandler("{$log_path}merged.log", 366));
        $this->setLogger($logger);
    }

    public function setSetupFunction($new_setup_function) {
        $this->setupFunction = $new_setup_function;
    }

    /** Override to handle custom requests. */
    public function parseInput() {
        global $_GET, $_POST;
        $input = [];
        $json_input = json_decode(file_get_contents('php://input'), true);
        if (is_array($json_input)) {
            // @codeCoverageIgnoreStart
            // Reason: php://input cannot be mocked.
            foreach ($json_input as $key => $value) {
                $input[$key] = $value;
            }
            // @codeCoverageIgnoreEnd
        }
        if (is_array($_POST)) {
            foreach ($_POST as $key => $value) {
                $input[$key] = json_decode($value, true);
            }
        }
        if (is_array($_GET)) {
            foreach ($_GET as $key => $value) {
                $input[$key] = json_decode($value, true);
            }
        }
        return $input;
    }

    public function call($raw_input) {
        if ($this->shouldFailThrottling()) {
            $this->logger->error("Throttled user request");
            throw new HttpError(429, "Zu viele Anfragen.");
        }

        try {
            $validated_input = backend_validate($this->getRequestFields(), $raw_input);
            $this->logger->info("Valid user request");
        } catch (ValidationError $verr) {
            $this->logger->warning("Bad user request", $verr->getStructuredAnswer());
            throw new HttpError(400, "Fehlerhafte Eingabe.", $verr);
        }

        try {
            $raw_result = $this->handle($validated_input);
        } catch (HttpError $http_error) {
            throw $http_error;
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->logger->critical("Unexpected endpoint error: {$message}", $exc->getTrace());
            throw new HttpError(500, "Es ist ein Fehler aufgetreten. Bitte später nochmals versuchen.", $exc);
        }

        try {
            $validated_result = backend_validate($this->getResponseFields(), $raw_result);
            $this->logger->info("Valid user response");
        } catch (ValidationError $verr) {
            $this->logger->critical("Bad output prohibited", $verr->getStructuredAnswer());
            throw new HttpError(500, "Es ist ein Fehler aufgetreten. Bitte später nochmals versuchen.", $verr);
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
