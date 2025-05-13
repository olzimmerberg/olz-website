<?php

namespace Olz\Apps\Members\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use Olz\Entity\Members\Member;
use PhpTypeScriptApi\HttpError;

/**
 * @extends OlzTypedEndpoint<
 *   array{},
 *   array{status: 'OK'|'ERROR', csvFileId?: ?non-empty-string}
 * >
 */
class ExportMembersEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        if (!$this->authUtils()->hasPermission('vorstand')) {
            throw new HttpError(403, "Kein Zugriff!");
        }
        $data_path = $this->envUtils()->getDataPath();
        $member_repo = $this->entityManager()->getRepository(Member::class);

        $user = $this->authUtils()->getCurrentUser();
        $this->log()->info("Members export by {$user?->getUsername()}.");

        $updates = [];
        foreach ($member_repo->findAll() as $member) {
            $user = $member->getUser();
            if (!$user) {
                continue;
            }
            $update = json_decode($member->getUpdates() ?? 'null', true);
            if (!$update) {
                continue;
            }
            $data = json_decode($member->getData(), true);
            $updates[] = [...$data, ...$update];
        }

        if (!$updates) {
            return ['status' => 'OK', 'csvFileId' => null];
        }

        $csv_file_id = $this->uploadUtils()->getRandomUploadId('.csv');
        $file_path = "{$data_path}temp/{$csv_file_id}";
        $fp = fopen($file_path, 'w');
        $this->generalUtils()->checkNotFalse($fp, "Could not open file {$file_path}");
        $fields = array_keys($updates[0]);
        fputcsv($fp, $fields, $separator = ",", $enclosure = "\"", $escape = "\\", $eol = "\n");
        foreach ($updates as $update) {
            $data = [];
            foreach ($fields as $field) {
                $value = $update[$field] ?? null;
                $this->generalUtils()->checkNotNull($value, "Update is missing field: {$field}");
                $data[] = $value;
            }
            fputcsv($fp, $data, $separator = ",", $enclosure = "\"", $escape = "\\", $eol = "\n");
        }
        fclose($fp);
        return ['status' => 'OK', 'csvFileId' => $csv_file_id];
    }
}
