<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\UploadUtils;

class FakeUploadUtils extends UploadUtils {
    /** @var array<array{0: ?array<string>, 1: string}> */
    public array $move_uploads_calls = [];

    public function getRandomUploadId(string $suffix): string {
        return "AAAAAAAAAAAAAAAAAAAAAAAA{$suffix}";
    }

    public function getValidUploadId(string $upload_id): ?string {
        if (substr($upload_id, 0, 7) === 'invalid') {
            return null;
        }
        return $upload_id ?: '-';
    }

    public function overwriteUploads(?array $upload_ids, string $new_base_path): void {
        $this->move_uploads_calls[] = [$upload_ids, $new_base_path];
    }
}
