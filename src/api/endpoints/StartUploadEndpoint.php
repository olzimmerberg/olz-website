<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class StartUploadEndpoint extends OlzEndpoint {
    const MAX_LOOP = 100;

    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG;
        require_once __DIR__.'/../../config/server.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../utils/auth/AuthUtils.php';
        require_once __DIR__.'/../../utils/GeneralUtils.php';
        $auth_utils = AuthUtils::fromEnv();
        $general_utils = GeneralUtils::fromEnv();
        $this->setAuthUtils($auth_utils);
        $this->setEnvUtils($_CONFIG);
        $this->setGeneralUtils($general_utils);
    }

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

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'suffix' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('any');
        if (!$has_access) {
            return ['status' => 'ERROR', 'id' => null];
        }

        $data_path = $this->envUtils->getDataPath();
        $temp_path = "{$data_path}temp/";
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0777, true);
        }
        $suffix = $input['suffix'] ?? '';
        $upload_id = '';
        $continue = true;
        for ($i = 0; $i < self::MAX_LOOP && $continue; $i++) {
            $upload_id = "{$this->getRandomUploadId()}{$suffix}";
            $upload_path = "{$temp_path}{$upload_id}";
            if (!is_file($upload_path)) {
                file_put_contents($upload_path, '');
                $continue = false;
            }
        }
        if ($continue) {
            $this->logger->error("Could not start upload. Finding unique ID failed. Maximum number of loops exceeded.");
            return ['status' => 'ERROR', 'id' => null];
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
