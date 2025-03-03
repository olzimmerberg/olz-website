<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\ImageUtils;
use Olz\Utils\WithUtilsCache;

class TestOnlyImageUtils extends ImageUtils {
    /** @var array<array{0: string, 1: string, 2: int}> */
    public array $getThumbFileCalls = [];

    public function getThumbFile(string $image_id, string $entity_img_path, int $size): string {
        $this->getThumbFileCalls[] = [$image_id, $entity_img_path, $size];
        return '';
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\ImageUtils
 */
final class ImageUtilsTest extends UnitTestCase {
    public function testOlzImage(): void {
        $image_utils = new ImageUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/news/123/img/abcd.jpg";
        mkdir(dirname($image_path), 0o777, true);
        copy($sample_image_path, $image_path);
        $attrs = '';
        $this->assertSame(
            <<<ZZZZZZZZZZ
                <span class='lightgallery'><a href='/data-href/img/news/123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news/123/img/abcd.jpg' onclick='event.stopPropagation()'>
                <img
                    src='/data-href/img/news/123/thumb/abcd.jpg\$128.jpg'
                    srcset='/data-href/img/news/123/thumb/abcd.jpg\$256.jpg 2x, /data-href/img/news/123/thumb/abcd.jpg\$128.jpg 1x'
                    alt=''
                    width='110'
                    height='73'
                    {$attrs}
                />
                </a></span>
                ZZZZZZZZZZ,
            $image_utils->olzImage('news', 123, 'abcd.jpg', 110),
        );
    }

    public function testGenerateThumbnails(): void {
        $image_utils = new TestOnlyImageUtils();

        $image_utils->generateThumbnails(['abcd.jpg', 'efgh.jpg'], 'fake-path');

        $this->assertSame([
            ['abcd.jpg', 'fake-path', 32],
            ['abcd.jpg', 'fake-path', 64],
            ['abcd.jpg', 'fake-path', 128],
            ['abcd.jpg', 'fake-path', 256],
            ['efgh.jpg', 'fake-path', 32],
            ['efgh.jpg', 'fake-path', 64],
            ['efgh.jpg', 'fake-path', 128],
            ['efgh.jpg', 'fake-path', 256],
        ], $image_utils->getThumbFileCalls);
    }

    public function testGetThumbFile(): void {
        $image_utils = new ImageUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $entity_img_path = "{$data_path}img/news/123/";
        mkdir("{$entity_img_path}img", 0o777, true);
        mkdir("{$entity_img_path}thumb", 0o777, true);
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$entity_img_path}img/abcd.jpg";
        copy($sample_image_path, $image_path);

        $thumbfile = $image_utils->getThumbFile('abcd.jpg', $entity_img_path, 128);

        $this->assertTrue(is_file($thumbfile));
        $this->assertSame("{$entity_img_path}thumb/abcd.jpg\$128.jpg", $thumbfile);
    }

    public function testGetThumbFileError(): void {
        $image_utils = new ImageUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $entity_img_path = "{$data_path}img/news/123/";

        try {
            $image_utils->getThumbFile('abcd.jpg', $entity_img_path, 110);
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame(
                'Size must be a power of two (32,64,128,256,512), was: 110',
                $exc->getMessage()
            );
        }
    }
}
