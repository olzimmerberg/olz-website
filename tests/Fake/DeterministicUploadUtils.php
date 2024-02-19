<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\UploadUtils;

class DeterministicUploadUtils extends UploadUtils {
    public $move_uploads_calls = [];

    public function getRandomUploadId($suffix) {
        return "AAAAAAAAAAAAAAAAAAAAAAAA{$suffix}";
    }

    public function getValidUploadId($upload_id) {
        if (substr($upload_id, 0, 7) === 'invalid') {
            return null;
        }
        return $upload_id;
    }

    public function overwriteUploads($upload_ids, $new_base_path) {
        $this->move_uploads_calls[] = [$upload_ids, $new_base_path];
    }
}
