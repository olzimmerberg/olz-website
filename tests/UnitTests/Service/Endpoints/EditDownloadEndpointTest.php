<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Entity\Service\Download;
use Olz\Service\Endpoints\EditDownloadEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeEditDownloadEndpointDownloadRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $entry = new Download();
            $entry->setId(12);
            $entry->setName('Fake Download');
            $entry->setPosition(12);
            $entry->setFileId('uploaded_file.pdf');
            return $entry;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $entry = new Download();
            $entry->setId(123);
            $entry->setName('Fake Download');
            $entry->setPosition(123);
            $entry->setFileId('uploaded_file.pdf');
            return $entry;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $entry = new Download();
            $entry->setId(1234);
            $entry->setName('Fake Download');
            $entry->setPosition(1234);
            $entry->setFileId('uploaded_file.pdf');
            $entry->setOnOff(true);
            return $entry;
        }
        if ($where === ['id' => 9999]) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\EditDownloadEndpoint
 */
final class EditDownloadEndpointTest extends UnitTestCase {
    public function testEditDownloadEndpointIdent(): void {
        $endpoint = new EditDownloadEndpoint();
        $this->assertSame('EditDownloadEndpoint', $endpoint->getIdent());
    }

    public function testEditDownloadEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new EditDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditDownloadEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeEditDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        $endpoint = new EditDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditDownloadEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeEditDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testEditDownloadEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeEditDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditDownloadEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'name' => 'Fake Download',
                'position' => 12,
                'fileId' => null,
            ],
        ], $result);
    }

    public function testEditDownloadEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeEditDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditDownloadEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'name' => 'Fake Download',
                'position' => 123,
                'fileId' => null,
            ],
        ], $result);
    }

    public function testEditDownloadEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeEditDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditDownloadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/downloads/');
        mkdir(__DIR__.'/../../tmp/files/downloads/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/downloads/1234/file1.pdf', '');

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'name' => 'Fake Download',
                'position' => 1234,
                'fileId' => 'file1.pdf',
            ],
        ], $result);
    }
}
