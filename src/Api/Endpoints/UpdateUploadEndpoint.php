<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

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
        $this->checkPermission('any');

        $data_path = $this->envUtils()->getDataPath();
        $upload_id = $input['id'];
        $upload_path = "{$data_path}temp/{$upload_id}";
        if (!is_file($upload_path)) {
            $this->log()->error("Could not update upload. Invalid ID: '{$upload_id}'.");
            return ['status' => 'ERROR'];
        }

        $part = $input['part'];
        $part_path = "{$upload_path}_{$part}";

        $content = $this->uploadUtils()->deobfuscateUpload($input['content']);
        file_put_contents($part_path, $content);

        return ['status' => 'OK'];
    }
}
