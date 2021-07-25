<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class StartUploadEndpoint extends Endpoint {
    const MAX_LOOP = 100;

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
        return 'StartUploadEndpoint';
    }

    public function getResponseFields() {
        return [
            'status' => new EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => new StringField(['allow_null' => true]),
        ];
    }

    public function getRequestFields() {
        return [];
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('any');
        if (!$has_access) {
            return ['status' => 'ERROR'];
        }

        $data_path = $this->envUtils->getDataPath();
        $temp_path = "{$data_path}temp/";
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0777, true);
        }
        $upload_id = '';
        $continue = true;
        for ($i = 0; $i < self::MAX_LOOP && $continue; $i++) {
            $upload_id = $this->getRandomUploadId();
            $upload_path = "{$temp_path}{$upload_id}";
            if (!is_file($upload_path)) {
                file_put_contents($upload_path, '');
                $continue = false;
            }
        }
        if ($continue) {
            $this->logger->error("Could not start upload. Finding unique ID failed. Maximum number of loops exceeded.");
            return ['status' => 'ERROR'];
        }
        return [
            'status' => 'OK',
            'id' => $upload_id,
        ];
    }

    protected function getRandomUploadId() {
        return $this->generalUtils->base64EncodeUrl(openssl_random_pseudo_bytes(18));
    }
}
