<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FileUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Utils\FileUtils
 */
final class FileUtilsTest extends UnitTestCase {
    public function testOlzFileNotMigrated(): void {
        $file_utils = new FileUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/downloads/123/001.pdf";
        mkdir(dirname($file_path), 0o777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame(
            "<a href='/data-href/files/downloads//123/001.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools/thumb/downloads\$123\$1\$16.svg); background-repeat:no-repeat;'>Test</a>",
            $file_utils->olzFile('downloads', 123, 1, "Test", 'test_file')
        );
    }

    public function testOlzFileMigrated(): void {
        $file_utils = new FileUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/news/123/abcdefghijklmnopqrstuvwx.pdf";
        mkdir(dirname($file_path), 0o777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame(
            "<span class='rendered-markdown'><a href='/data-href/files/news//123/abcdefghijklmnopqrstuvwx.pdf?modified=1584127800' download='test_file.pdf'>Test</a></span>",
            $file_utils->olzFile('news', 123, 'abcdefghijklmnopqrstuvwx.pdf', "Test", 'test_file')
        );
    }
}
