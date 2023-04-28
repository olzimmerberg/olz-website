<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\ImageUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\ImageUtils
 */
final class ImageUtilsTest extends UnitTestCase {
    public function testOlzImage(): void {
        $image_utils = new ImageUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $image_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/news/123/img/abcd.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $attrs = '';
        $this->assertSame(
            <<<ZZZZZZZZZZ
            <span class='lightgallery'><a href='/data-href/img/news//123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news//123/img/abcd.jpg' onclick='event.stopPropagation()'>
            <img
                src='/_/image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=110'
                srcset='/_/image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=220 2x, /_/image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=110 1x'
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
        $env_utils = new Fake\FakeEnvUtils();
        $image_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/news/123/img/abcd.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $attrs = '';
        $this->assertSame(
            <<<ZZZZZZZZZZ
            test <span class='lightgallery'><a href='/data-href/img/news//123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news//123/img/abcd.jpg' onclick='event.stopPropagation()'>
            <img
                src='/_/image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=110'
                srcset='/_/image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=220 2x, /_/image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=110 1x'
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
