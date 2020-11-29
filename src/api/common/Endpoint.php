<?php

require_once __DIR__.'/api.php';
require_once __DIR__.'/validate.php';
require_once __DIR__.'/../../utils/session/AbstractSession.php';

class HttpError extends Exception {
    public function __construct($http_status_code, $message, Exception $previous = null) {
        parent::__construct($message, $http_status_code, $previous);
    }

    public function getStructuredAnswer() {
        $structured_previous_error = true;
        $previous_exception = $this->getPrevious();
        if ($previous_exception && method_exists($previous_exception, 'getStructuredAnswer')) {
            $structured_previous_error = $previous_exception->getStructuredAnswer();
        }
        return [
            'message' => $this->getMessage(),
            'error' => $structured_previous_error,
        ];
    }
}

abstract class Endpoint {
    protected ?AbstractSession $session = null;

    public function call($raw_input) {
        try {
            $validated_input = backend_validate($this->getRequestFields(), $raw_input);
        } catch (ValidationError $verr) {
            throw new HttpError(400, "TODO: request structured answer", $verr);
        }

        try {
            $raw_result = $this->handle($validated_input);
        } catch (\Exception $exc) {
            throw new HttpError(500, "TODO: error structured answer", $exc);
        }

        try {
            $validated_result = backend_validate($this->getResponseFields(), $raw_result);
        } catch (ValidationError $verr) {
            throw new HttpError(500, "TODO: response structured answer", $verr);
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
