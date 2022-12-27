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
    public function testOlzImageNotMigrated(): void {
        $image_utils = new ImageUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $image_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/aktuell/123/img/001.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $this->assertSame("<span class='lightgallery'><a href='/data-href/img/aktuell//123/img/001.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/aktuell//123/img/001.jpg' onclick='event.stopPropagation()'><img src='image_tools.php?request=thumb&db_table=aktuell&id=123&index=1&dim=110' alt='' width='110' height='73'></a></span>", $image_utils->olzImage('aktuell', 123, 1, 110));
    }

    public function testOlzImageMigrated(): void {
        $image_utils = new ImageUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $image_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/news/123/img/abcd.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $this->assertSame("<span class='lightgallery'><a href='/data-href/img/news//123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news//123/img/abcd.jpg' onclick='event.stopPropagation()'><img src='image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=110' alt='' width='110' height='73'></a></span>", $image_utils->olzImage('news', 123, 'abcd.jpg', 110));
    }

    public function testReplaceImageTagsNotMigrated(): void {
        $image_utils = new ImageUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $image_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/aktuell/123/img/001.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $this->assertSame("test <span class='lightgallery'><a href='/data-href/img/aktuell//123/img/001.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/aktuell//123/img/001.jpg' onclick='event.stopPropagation()'><img src='image_tools.php?request=thumb&db_table=aktuell&id=123&index=1&dim=110' alt='' width='110' height='73'></a></span> text", $image_utils->replaceImageTags('test <BILD1> text', 123, null));
    }

    public function testReplaceImageTagsMigrated(): void {
        $image_utils = new ImageUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $image_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_image_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-picture.jpg';
        $image_path = "{$data_path}img/news/123/img/abcd.jpg";
        mkdir(dirname($image_path), 0777, true);
        copy($sample_image_path, $image_path);
        $this->assertSame("test <span class='lightgallery'><a href='/data-href/img/news//123/img/abcd.jpg' aria-label='Bild vergrössern' data-src='/data-href/img/news//123/img/abcd.jpg' onclick='event.stopPropagation()'><img src='image_tools.php?request=thumb&db_table=news&id=123&index=abcd.jpg&dim=110' alt='' width='110' height='73'></a></span> text", $image_utils->replaceImageTags('test <BILD1> text', 123, ['abcd.jpg']));
    }
}
