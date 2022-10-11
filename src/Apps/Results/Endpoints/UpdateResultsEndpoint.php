<?php

namespace Olz\Apps\Results\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;

class UpdateResultsEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateResultsEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'INVALID_FILENAME',
                'INVALID_BASE64_DATA',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'file' => new FieldTypes\StringField(['allow_null' => false]),
            'content' => new FieldTypes\StringField(['allow_null' => false]),
        ]]);
    }

    protected function handle($input) {
        require_once __DIR__.'/../../../_/config/paths.php';

        $filename = $input['file'];
        $is_filename_ok = preg_match('/^[a-z0-9\-\.]+$/', $filename);
        if (!$is_filename_ok) {
            $this->log()->warning("Filename must match ^[a-z0-9\\-\\.]+$: {$filename}");
            return ['status' => 'INVALID_FILENAME'];
        }
        $results_data_path = realpath("{$data_path}results");
        $file_path = "{$results_data_path}/{$filename}";
        if (is_file($file_path)) {
            rename($file_path, $file_path.".bak_".olz_current_date("Y-m-dTH:i:s"));
        }
        $new_content = base64_decode($input['content']);
        if (!$new_content) {
            $this->log()->warning("Invalid base64 data");
            return ['status' => 'INVALID_BASE64_DATA'];
        }
        file_put_contents($file_path, $new_content);
        file_put_contents(
            "{$results_data_path}/_live.json",
            json_encode([
                'file' => $filename,
                'last_updated_at' => olz_current_date('Y-m-d H:i:s'),
            ]),
        );
        return ['status' => 'OK'];
    }
}
