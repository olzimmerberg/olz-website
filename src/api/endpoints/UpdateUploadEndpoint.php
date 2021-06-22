<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class UpdateUploadEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function setGeneralUtils($generalUtils) {
        $this->generalUtils = $generalUtils;
    }

    public static function getIdent() {
        return 'UpdateUploadEndpoint';
    }

    public function getResponseFields() {
        return [
            new EnumField('status', ['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ];
    }

    public function getRequestFields() {
        return [
            new StringField('id', ['allow_null' => false]),
            new IntegerField('part', ['allow_null' => false, 'min_value' => 0, 'max_value' => 1000]),
            new StringField('content', ['allow_null' => false]),
        ];
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('any');
        if (!$has_access) {
            return ['status' => 'ERROR'];
        }

        $data_path = $this->envUtils->getDataPath();
        $upload_id = $input['id'];
        $upload_path = "{$data_path}temp/{$upload_id}";
        if (!is_file($upload_path)) {
            $this->logger->error("Could not update upload. Invalid ID: '{$upload_id}'.");
            return ['status' => 'ERROR'];
        }

        $part = $input['part'];
        $part_path = "{$upload_path}_{$part}";

        $content = $this->generalUtils->deobfuscateUpload($input['content']);
        file_put_contents($part_path, $content);

        return ['status' => 'OK'];
    }
}
