<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\ImageUtils;
use Olz\Utils\WithUtilsCache;

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
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $attrs = '';
        $this->assertSame(
            <<<ZZZZZZZZZZ
            <span class='lightgallery'><a href='/data-href/img/news//123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news//123/img/abcd.jpg' onclick='event.stopPropagation()'>
            <img
                src='/_/image_tools/thumb/news\$123\$abcd.jpg\$110.jpg'
                srcset='/_/image_tools/thumb/news\$123\$abcd.jpg\$220.jpg 2x, /_/image_tools/thumb/news\$123\$abcd.jpg\$110.jpg 1x'
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

    public function testReplaceImageTags(): void {
        $image_utils = new ImageUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/news/123/img/abcd.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $attrs = '';
        $this->assertSame(
            <<<ZZZZZZZZZZ
            test <span class='lightgallery'><a href='/data-href/img/news//123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news//123/img/abcd.jpg' onclick='event.stopPropagation()'>
            <img
                src='/_/image_tools/thumb/news\$123\$abcd.jpg\$110.jpg'
                srcset='/_/image_tools/thumb/news\$123\$abcd.jpg\$220.jpg 2x, /_/image_tools/thumb/news\$123\$abcd.jpg\$110.jpg 1x'
                alt=''
                width='110'
                height='73'
                {$attrs}
            />
            </a></span> text
            ZZZZZZZZZZ,
            $image_utils->replaceImageTags('test <BILD1> text', 123, ['abcd.jpg']),
        );
    }
}
