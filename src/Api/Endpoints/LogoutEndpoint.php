<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class LogoutEndpoint extends OlzEndpoint {
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
        $this->session()->delete('auth');
        $this->session()->delete('root');
        $this->session()->delete('user');
        $this->session()->delete('user_id');
        $this->session()->delete('auth_user');
        $this->session()->delete('auth_user_id');
        $this->session()->clear();
        return [
            'status' => 'SESSION_CLOSED',
        ];
    }
}
