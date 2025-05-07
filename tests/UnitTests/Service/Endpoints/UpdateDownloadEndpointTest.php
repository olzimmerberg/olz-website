<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\UpdateDownloadEndpoint;
use Olz\Tests\Fake\Entity\Service\FakeDownload;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\UpdateDownloadEndpoint
 */
final class UpdateDownloadEndpointTest extends UnitTestCase {
    public function testUpdateDownloadEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'name' => 'Test Download',
                    'position' => 3,
                    'fileId' => 'uploaded_file.pdf',
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateDownloadEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'name' => 'Test Download',
                    'position' => 3,
                    'fileId' => 'uploaded_file.pdf',
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateDownloadEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateDownloadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'name' => 'Test Download',
                    'position' => 3,
                    'fileId' => 'uploaded_file.pdf',
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeDownload::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'downloads'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateDownloadEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateDownloadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file.pdf', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/downloads/');

        $result = $endpoint->call([
            'id' => 123,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'name' => 'Test Download',
                'position' => 3,
                'fileId' => 'uploaded_file.pdf',
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => 123,
        ], $result);

        $this->assertSame([
            [FakeDownload::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'downloads'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $download = $entity_manager->persisted[0];
        $this->assertSame(123, $download->getId());
        $this->assertSame('Test Download', $download->getName());
        $this->assertSame(3, $download->getPosition());

        $this->assertSame([
            [$download, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_file.pdf'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/files/downloads/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
