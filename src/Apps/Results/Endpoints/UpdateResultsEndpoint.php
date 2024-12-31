<?php

namespace Olz\Apps\Results\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;
use PhpTypeScriptApi\TypedEndpoint;

/**
 * @extends TypedEndpoint<
 *   array{
 *     file: non-empty-string,
 *     content?: ?non-empty-string,
 *     iofXmlFileId?: ?non-empty-string,
 *   },
 *   array{status: 'OK'|'INVALID_FILENAME'|'INVALID_BASE64_DATA'|'ERROR'}
 * >
 */
class UpdateResultsEndpoint extends TypedEndpoint {
    use OlzTypedEndpoint;

    public static function getApiObjectClasses(): array {
        return [];
    }

    public static function getIdent(): string {
        return 'UpdateResultsEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $data_path = $this->envUtils()->getDataPath();
        $filename = $input['file'];
        $is_filename_ok = preg_match('/^[a-z0-9\-\.]+$/', $filename);
        if (!$is_filename_ok) {
            $this->log()->warning("Filename must match ^[a-z0-9\\-\\.]+$: {$filename}");
            return ['status' => 'INVALID_FILENAME'];
        }
        $results_data_path = realpath("{$data_path}results");
        $file_path = "{$results_data_path}/{$filename}";
        if (is_file($file_path)) {
            $iso_t_now = $this->dateUtils()->getCurrentDateInFormat("Y-m-d_H:i:s");
            rename(
                $file_path,
                $file_path.".bak_".$iso_t_now,
            );
        }
        if ($input['content'] ?? false) {
            $new_content = base64_decode($input['content']);
            if (!$new_content) {
                $this->log()->warning("Invalid base64 data");
                return ['status' => 'INVALID_BASE64_DATA'];
            }
            file_put_contents($file_path, $new_content);
        } elseif ($input['iofXmlFileId'] ?? false) {
            $upload_id = $input['iofXmlFileId'];
            $upload_path = "{$data_path}temp/{$upload_id}";
            if (!is_file($upload_path)) {
                throw new HttpError(400, 'Uploaded file not found!');
            }
            rename($upload_path, $file_path);
        } else {
            throw new HttpError(400, 'Either iofXmlFileId or content must be set!');
        }

        $iso_now = $this->dateUtils()->getCurrentDateInFormat('Y-m-d H:i:s');
        file_put_contents(
            "{$results_data_path}/_live.json",
            json_encode([
                'file' => $filename,
                'last_updated_at' => $iso_now,
            ]),
        );
        return ['status' => 'OK'];
    }
}
