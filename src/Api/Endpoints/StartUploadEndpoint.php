<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     suffix?: ?non-empty-string,
 *   },
 *   array{
 *     status: 'OK'|'ERROR',
 *     id?: ?non-empty-string
 *   }
 * >
 */
class StartUploadEndpoint extends OlzTypedEndpoint {
    public const MAX_LOOP = 100;

    protected function handle(mixed $input): mixed {
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
