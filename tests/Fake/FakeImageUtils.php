<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\ImageUtils;

class FakeImageUtils extends ImageUtils {
    /** @var array<array{0: array<string>, 1: string}> */
    public array $generatedThumbnails = [];

    /** @param array<string> $image_ids */
    public function generateThumbnails(array $image_ids, string $entity_img_path): void {
        $this->generatedThumbnails[] = [$image_ids, $entity_img_path];
    }
}
