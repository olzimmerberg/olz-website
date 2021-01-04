<?php

require_once __DIR__.'/api.php';
require_once __DIR__.'/HttpError.php';
require_once __DIR__.'/validate.php';
require_once __DIR__.'/../../utils/session/AbstractSession.php';

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
