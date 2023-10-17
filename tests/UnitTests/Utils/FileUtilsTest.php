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
        mkdir(dirname($file_path), 0777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame("<a href='/data-href/files/downloads//123/001.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools.php?request=thumb&db_table=downloads&id=123&index=1&dim=16); background-repeat:no-repeat;'>Test</a>", $file_utils->olzFile('downloads', 123, 1, "Test"));
    }

    public function testOlzFileMigrated(): void {
        $file_utils = new FileUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/news/123/abcdefghijklmnopqrstuvwx.pdf";
        mkdir(dirname($file_path), 0777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame("<a href='/data-href/files/news//123/abcdefghijklmnopqrstuvwx.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools.php?request=thumb&db_table=news&id=123&index=abcdefghijklmnopqrstuvwx.pdf&dim=16); background-repeat:no-repeat;'>Test</a>", $file_utils->olzFile('news', 123, 'abcdefghijklmnopqrstuvwx.pdf', "Test"));
    }

    public function testReplaceFileTagsNotMigrated(): void {
        $file_utils = new FileUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/downloads/123/001.pdf";
        mkdir(dirname($file_path), 0777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame("test <a href='/data-href/files/downloads//123/001.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools.php?request=thumb&db_table=downloads&id=123&index=1&dim=16); background-repeat:no-repeat;'>Datei</a> text", $file_utils->replaceFileTags('test <DATEI1 text="Datei"> text', 'downloads', 123));
    }

    public function testReplaceFileTagsMigrated(): void {
        $file_utils = new FileUtils();
        $data_path = WithUtilsCache::get('envUtils')->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/news/123/abcdefghijklmnopqrstuvwx.pdf";
        mkdir(dirname($file_path), 0777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame("test <a href='/data-href/files/news//123/abcdefghijklmnopqrstuvwx.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools.php?request=thumb&db_table=news&id=123&index=abcdefghijklmnopqrstuvwx.pdf&dim=16); background-repeat:no-repeat;'>Datei</a> text", $file_utils->replaceFileTags('test <DATEI=abcdefghijklmnopqrstuvwx.pdf text="Datei"> text', 'news', 123));
    }
}
