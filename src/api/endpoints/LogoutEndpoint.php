<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/EnumField.php';

class LogoutEndpoint extends Endpoint {
    public static function getIdent() {
        return 'LogoutEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'NO_SESSION',
                'SESSION_CLOSED',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [];
    }

    protected function handle($input) {
        $this->session->delete('auth');
        $this->session->delete('root');
        $this->session->delete('user');
        $this->session->delete('user_id');
        $this->session->clear();
        return [
            'status' => 'SESSION_CLOSED',
        ];
    }
}
