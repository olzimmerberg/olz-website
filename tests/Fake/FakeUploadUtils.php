<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeUploadUtils {
    public $move_uploads_calls = [];

    public function getValidUploadIds($upload_ids) {
        return $upload_ids;
    }

    public function moveUploads($upload_ids, $new_base_path) {
        $this->move_uploads_calls[] = [$upload_ids, $new_base_path];
    }
}
