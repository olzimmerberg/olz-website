<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class LogoutEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
    }

    public static function getIdent() {
        return 'LogoutEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'NO_SESSION',
                'SESSION_CLOSED',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField([
            'field_structure' => [],
            'allow_null' => true,
        ]);
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
