<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class StartUploadEndpoint extends OlzEndpoint {
    public const MAX_LOOP = 100;

    public static function getIdent(): string {
        return 'StartUploadEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'suffix' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $data_path = $this->envUtils()->getDataPath();
        $temp_path = "{$data_path}temp/";
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0o777, true);
        }
        $suffix = $input['suffix'] ?? '';
        $upload_id = '';
        $continue = true;
        for ($i = 0; $i < self::MAX_LOOP && $continue; $i++) {
            $upload_id = $this->uploadUtils()->getRandomUploadId($suffix);
            $upload_path = "{$temp_path}{$upload_id}";
            if (!is_file($upload_path)) {
                file_put_contents($upload_path, '');
                $continue = false;
            }
        }
        if ($continue) {
            $this->log()->error("Could not start upload. Finding unique ID failed. Maximum number of loops exceeded.");
            return ['status' => 'ERROR', 'id' => null];
        }
        return [
            'status' => 'OK',
            'id' => $upload_id,
        ];
    }
}
