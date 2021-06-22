<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../../fields/DateTimeField.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class FinishUploadEndpoint extends Endpoint {
    public function setAuthUtils($new_auth_utils) {
        $this->authUtils = $new_auth_utils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public static function getIdent() {
        return 'FinishUploadEndpoint';
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
            new IntegerField('numberOfParts', ['allow_null' => false, 'min_value' => 1, 'max_value' => 1000]),
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
            $this->logger->error("Could not finish upload. Invalid ID: '{$upload_id}'.");
            return ['status' => 'ERROR'];
        }

        $num_parts = $input['numberOfParts'];
        $first_part_path = "{$upload_path}_0";
        if (!is_file($first_part_path)) {
            $this->logger->error("Upload with ID {$upload_id} is missing the first part.");
            return ['status' => 'ERROR'];
        }
        $first_content = file_get_contents($first_part_path);
        @unlink($first_part_path);
        $res = preg_match("/^data\\:([^\\;]*)\\;base64\\,(.+)$/", $first_content, $matches);
        if (!$res) {
            $this->logger->error("Upload with ID {$upload_id} does not have base64 header.");
            return ['status' => 'ERROR'];
        }
        $mime_type = $matches[1];
        $base64 = $matches[2];
        $missing_parts = [];
        for ($part = 1; $part < $num_parts; $part++) {
            $part_path = "{$upload_path}_{$part}";
            if (!is_file($part_path)) {
                $missing_parts[] = $part;
                continue;
            }
            $part_content = file_get_contents($part_path);
            $base64 .= $part_content;
            @unlink($part_path);
        }
        if (count($missing_parts) > 0) {
            $pretty_missing_parts = implode(', ', $missing_parts);
            $this->logger->error("Upload with ID {$upload_id} is missing parts {$pretty_missing_parts}.");
            return ['status' => 'ERROR'];
        }
        $binary_data = base64_decode(str_replace(" ", "+", $base64));

        file_put_contents($upload_path, $binary_data);

        return ['status' => 'OK'];
    }
}
