<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Entity\Service\Download;
use Olz\Service\Endpoints\GetDownloadEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetDownloadEndpointDownloadRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $entry = new Download();
            $entry->setId(12);
            $entry->setName('Fake Download');
            $entry->setPosition(12);
            $entry->setFileId('file1.pdf');
            return $entry;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $entry = new Download();
            $entry->setId(123);
            $entry->setName('Fake Download');
            $entry->setPosition(123);
            $entry->setFileId('file1.pdf');
            return $entry;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $entry = new Download();
            $entry->setId(1234);
            $entry->setName('Fake Download');
            $entry->setPosition(1234);
            $entry->setFileId('file1.pdf');
            $entry->setOnOff(true);
            return $entry;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\GetDownloadEndpoint
 */
final class GetDownloadEndpointTest extends UnitTestCase {
    public function testGetDownloadEndpointIdent(): void {
        $endpoint = new GetDownloadEndpoint();
        $this->assertSame('GetDownloadEndpoint', $endpoint->getIdent());
    }

    public function testGetDownloadEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetDownloadEndpoint();
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

    public function testGetDownloadEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeGetDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        $endpoint = new GetDownloadEndpoint();
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

    public function testGetDownloadEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeGetDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        $endpoint = new GetDownloadEndpoint();
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

    public function testGetDownloadEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $download_repo = new FakeGetDownloadEndpointDownloadRepository();
        $entity_manager->repositories[Download::class] = $download_repo;
        $endpoint = new GetDownloadEndpoint();
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
