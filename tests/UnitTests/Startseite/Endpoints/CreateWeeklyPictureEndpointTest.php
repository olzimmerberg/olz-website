<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Startseite\Endpoints;

use Olz\Startseite\Endpoints\CreateWeeklyPictureEndpoint;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Startseite\Endpoints\CreateWeeklyPictureEndpoint
 */
final class CreateWeeklyPictureEndpointTest extends UnitTestCase {
    public function testCreateWeeklyPictureEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['weekly_picture' => false];
        $endpoint = new CreateWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'text' => 'Test Titel',
                    'imageId' => 'invalid.jpg',
                    'publishedDate' => null,
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

    public function testCreateWeeklyPictureEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['weekly_picture' => true];
        $endpoint = new CreateWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/weekly_picture/');

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'text' => 'Test Titel',
                'imageId' => 'uploaded_image.jpg',
                'publishedDate' => null,
            ],
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $weekly_picture = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $weekly_picture->getId());
        $this->assertSame('2020-03-13', $weekly_picture->getPublishedDate()->format('Y-m-d'));
        $this->assertSame('Test Titel', $weekly_picture->getText());
        $this->assertSame('uploaded_image.jpg', $weekly_picture->getImageId());

        $this->assertSame([
            [$weekly_picture, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/weekly_picture/{$id}/img/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
            [
                ['uploaded_image.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/weekly_picture/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testCreateWeeklyPictureEndpointInvalidPicture(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['weekly_picture' => true];
        $endpoint = new CreateWeeklyPictureEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_image.jpg', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/weekly_picture/');

        try {
            $endpoint->call([
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'text' => 'Test Titel',
                    'imageId' => 'invalid.jpg',
                    'publishedDate' => null,
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400",
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode());

            $entity_manager = WithUtilsCache::get('entityManager');
            $this->assertCount(0, $entity_manager->persisted);
            $this->assertCount(0, $entity_manager->flushed_persisted);

            // The entity is created, but not persisted.
            $this->assertCount(1, WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

            $this->assertSame([
            ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
            $this->assertSame([
            ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
        }
    }
}
