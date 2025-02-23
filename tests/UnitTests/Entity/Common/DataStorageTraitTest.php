<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Entity\Common;

use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class DataStorageTraitConcreteEntity implements DataStorageInterface {
    use DataStorageTrait;

    public function getEntityIdForStorage(): string {
        return '123';
    }

    public static function getEntityNameForStorage(): string {
        return 'concrete_entity';
    }
}

/**
 * @internal
 *
 * @covers \Olz\Entity\Common\DataStorageTrait
 */
final class DataStorageTraitTest extends UnitTestCase {
    public function testGetStoredImageUploadIds(): void {
        $this->writeImagesToDisk();
        $storage_entity = new DataStorageTraitConcreteEntity();

        $this->assertSame(
            ['abcdefghijklmnopqrstuvw1.jpg', 'abcdefghijklmnopqrstuvw2.jpg'],
            $storage_entity->getStoredImageUploadIds()
        );
    }

    public function testGetStoredFileUploadIds(): void {
        $this->writeFilesToDisk();
        $storage_entity = new DataStorageTraitConcreteEntity();

        $this->assertSame(
            ['abcdefghijklmnopqrstuvw1.pdf', 'abcdefghijklmnopqrstuvw2.pdf'],
            $storage_entity->getStoredFileUploadIds()
        );
    }

    public function testReplaceImagePaths(): void {
        $this->writeImagesToDisk();
        $storage_entity = new DataStorageTraitConcreteEntity();

        $this->assertSame(
            "before <span style='color:#ff0000; font-style:italic;'>Ungültige db_table: concrete_entity (in olzImage)</span> between <span style='color:#ff0000; font-style:italic;'>Ungültige db_table: concrete_entity (in olzImage)</span> after",
            $storage_entity->replaceImagePaths('before <img src="./abcdefghijklmnopqrstuvw2.jpg" alt="" /> between <img src="./abcdefghijklmnopqrstuvw1.jpg" alt="" /> after')
        );
    }

    public function testReplaceFilePaths(): void {
        $this->writeFilesToDisk();
        $storage_entity = new DataStorageTraitConcreteEntity();

        $this->assertSame(
            'before <a href="/data-href/files/concrete_entity/123/abcdefghijklmnopqrstuvw2.pdf?modified=2020-03-13_19-30-00">Datei2</a> between <a href="/data-href/files/concrete_entity/123/abcdefghijklmnopqrstuvw1.pdf?modified=2020-03-13_19-30-00" class="test">Datei 1</a> after',
            $storage_entity->replaceFilePaths('before <a href="./abcdefghijklmnopqrstuvw2.pdf">Datei2</a> between <a href="./abcdefghijklmnopqrstuvw1.pdf" class="test">Datei 1</a> after')
        );
    }

    public function testGetFileHref(): void {
        $this->writeFilesToDisk();
        $storage_entity = new DataStorageTraitConcreteEntity();

        $this->assertSame(
            '/data-href/files/concrete_entity/123/abcdefghijklmnopqrstuvw1.pdf?modified=2020-03-13_19-30-00',
            $storage_entity->getFileHref('abcdefghijklmnopqrstuvw1.pdf')
        );
    }

    public function testGetImagesPathForStorage(): void {
        $storage_entity = new DataStorageTraitConcreteEntity();
        $this->assertSame(
            realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/concrete_entity/123/",
            $storage_entity->getImagesPathForStorage()
        );
    }

    public function testGetFilesPathForStorage(): void {
        $storage_entity = new DataStorageTraitConcreteEntity();
        $this->assertSame(
            realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/concrete_entity/123/",
            $storage_entity->getFilesPathForStorage()
        );
    }

    public function testGetEntityPathForStorage(): void {
        $storage_entity = new DataStorageTraitConcreteEntity();
        $this->assertSame(
            'concrete_entity/123/',
            $storage_entity->getEntityPathForStorage()
        );
    }

    protected function writeImagesToDisk(): void {
        $base_path = __DIR__.'/../../tmp/img/concrete_entity/123/img/';
        mkdir($base_path, 0o777, true);
        file_put_contents(
            "{$base_path}abcdefghijklmnopqrstuvw1.jpg",
            '',
        );
        touch("{$base_path}abcdefghijklmnopqrstuvw1.jpg", strtotime('2020-03-13 19:30:00'));
        file_put_contents(
            "{$base_path}abcdefghijklmnopqrstuvw2.jpg",
            '',
        );
        touch("{$base_path}abcdefghijklmnopqrstuvw2.jpg", strtotime('2020-03-13 19:30:00'));
        file_put_contents(
            "{$base_path}invalid-upload-id.jpg",
            '',
        );
    }

    protected function writeFilesToDisk(): void {
        $base_path = __DIR__.'/../../tmp/files/concrete_entity/123/';
        mkdir($base_path, 0o777, true);
        file_put_contents(
            "{$base_path}abcdefghijklmnopqrstuvw1.pdf",
            '',
        );
        touch("{$base_path}abcdefghijklmnopqrstuvw1.pdf", strtotime('2020-03-13 19:30:00'));
        file_put_contents(
            "{$base_path}abcdefghijklmnopqrstuvw2.pdf",
            '',
        );
        touch("{$base_path}abcdefghijklmnopqrstuvw2.pdf", strtotime('2020-03-13 19:30:00'));
        file_put_contents(
            "{$base_path}invalid-upload-id.pdf",
            '',
        );
    }
}
