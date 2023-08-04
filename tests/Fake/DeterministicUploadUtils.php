<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\UploadUtils;

class DeterministicUploadUtils extends UploadUtils {
    public $move_uploads_calls = [];

    public function getRandomUploadId($suffix) {
        return "AAAAAAAAAAAAAAAAAAAAAAAA{$suffix}";
    }

    public function getValidUploadIds($upload_ids) {
        return $upload_ids;
    }

    public function getValidUploadId($upload_id) {
        return $upload_id;
    }

    public function overwriteUploads($upload_ids, $new_base_path) {
        $this->move_uploads_calls[] = [$upload_ids, $new_base_path];
    }
}
