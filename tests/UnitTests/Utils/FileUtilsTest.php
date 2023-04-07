<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FileUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\FileUtils
 */
final class FileUtilsTest extends UnitTestCase {
    public function testOlzFile(): void {
        $file_utils = new FileUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $file_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/news/123/abcdefghijklmnopqrstuvwx.pdf";
        mkdir(dirname($file_path), 0777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame("<a href='/data-href/files/news//123/abcdefghijklmnopqrstuvwx.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools.php?request=thumb&db_table=news&id=123&index=abcdefghijklmnopqrstuvwx.pdf&dim=16); background-repeat:no-repeat;'>Test</a>", $file_utils->olzFile('news', 123, 'abcdefghijklmnopqrstuvwx.pdf', "Test"));
    }

    public function testReplaceFileTags(): void {
        $file_utils = new FileUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $file_utils->setEnvUtils($env_utils);
        $data_path = $env_utils->getDataPath();
        $sample_file_path = __DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf';
        $file_path = "{$data_path}files/news/123/abcdefghijklmnopqrstuvwx.pdf";
        mkdir(dirname($file_path), 0777, true);
        copy($sample_file_path, $file_path);
        touch($file_path, strtotime('2020-03-13 19:30:00'));
        $this->assertSame("test <a href='/data-href/files/news//123/abcdefghijklmnopqrstuvwx.pdf?modified=1584127800' style='padding-left:19px; background-image:url(/_/file_tools.php?request=thumb&db_table=news&id=123&index=abcdefghijklmnopqrstuvwx.pdf&dim=16); background-repeat:no-repeat;'>Datei</a> text", $file_utils->replaceFileTags('test <DATEI=abcdefghijklmnopqrstuvwx.pdf text="Datei"> text', 'news', 123));
    }
}
