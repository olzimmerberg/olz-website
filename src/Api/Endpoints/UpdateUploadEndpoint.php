<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzTypedEndpoint;

/**
 * @extends OlzTypedEndpoint<
 *   array{
 *     id: non-empty-string,
 *     part: int<0, 1000>,
 *     content:  non-empty-string,
 *   },
 *   array{
 *     status: 'OK'|'ERROR',
 *   }
 * >
 */
class UpdateUploadEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
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
