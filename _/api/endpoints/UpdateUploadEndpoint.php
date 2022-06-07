<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';

class UpdateUploadEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateUploadEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\StringField(['allow_null' => false]),
            'part' => new FieldTypes\IntegerField(['allow_null' => false, 'min_value' => 0, 'max_value' => 1000]),
            'content' => new FieldTypes\StringField(['allow_null' => false]),
        ]]);
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

        $content = $this->uploadUtils->deobfuscateUpload($input['content']);
        file_put_contents($part_path, $content);

        return ['status' => 'OK'];
    }
}