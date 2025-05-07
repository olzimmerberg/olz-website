<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Startseite\Endpoints\UpdateWeeklyPictureEndpoint;
use Olz\Tests\Fake\Entity\Startseite\FakeWeeklyPicture;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Startseite\Endpoints\UpdateWeeklyPictureEndpoint
 */
final class UpdateWeeklyPictureEndpointTest extends UnitTestCase {
    public function testUpdateWeeklyPictureEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateWeeklyPictureEndpoint();
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
                    'publishedDate' => '2020-03-16',
                    'text' => 'Test text',
                    'imageId' => 'uploaded_image.jpg',
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

    public function testUpdateWeeklyPictureEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateWeeklyPictureEndpoint();
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
                    'publishedDate' => '2020-03-16',
                    'text' => 'Test text',
                    'imageId' => 'uploaded_image.jpg',
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

    public function testUpdateWeeklyPictureEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateWeeklyPictureEndpoint();
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
                    'publishedDate' => '2020-03-16',
                    'text' => 'Test text',
                    'imageId' => 'uploaded_image.jpg',
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeWeeklyPicture::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'weekly_picture'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateWeeklyPictureEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file.pdf', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/WeeklyPicture/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/WeeklyPicture/');

        $result = $endpoint->call([
            'id' => 123,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'text' => 'Test Titel',
                'imageId' => 'uploaded_image.jpg',
                'publishedDate' => '2020-03-16',
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
            [FakeWeeklyPicture::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'weekly_picture'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(123, $entity->getId());
        $this->assertSame('2020-03-16', $entity->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('Test Titel', $entity->getText());
        $this->assertSame('uploaded_image.jpg', $entity->getImageId());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/weekly_picture/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/weekly_picture/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateWeeklyPictureEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'text' => '',
                'imageId' => 'uploaded_image.jpg',
                'publishedDate' => null,
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
            [FakeWeeklyPicture::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'weekly_picture'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(123, $entity->getId());
        $this->assertSame('2020-03-13', $entity->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('', $entity->getText());
        $this->assertSame('uploaded_image.jpg', $entity->getImageId());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/weekly_picture/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/weekly_picture/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
