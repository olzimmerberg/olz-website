<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\DeleteNewsEndpoint;
use Olz\Tests\Fake\Entity\News\FakeNews;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\DeleteNewsEndpoint
 */
final class DeleteNewsEndpointTest extends UnitTestCase {
    public function testDeleteNewsEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new DeleteNewsEndpoint();
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

    public function testDeleteNewsEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new DeleteNewsEndpoint();
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
            $this->assertSame([
                [FakeNews::empty(), null, null, null, null, 'news'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testDeleteNewsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteNewsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([], $result);
        $this->assertSame([
            [FakeNews::empty(), null, null, null, null, 'news'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $news_entry = $entity_manager->persisted[0];
        $this->assertSame(123, $news_entry->getId());
        $this->assertSame(0, $news_entry->getOnOff());
    }

    public function testDeleteNewsEndpointInexistent(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteNewsEndpoint();
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
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls
            );
            $this->assertSame(404, $err->getCode());
            $entity_manager = WithUtilsCache::get('entityManager');
            $this->assertCount(0, $entity_manager->removed);
            $this->assertCount(0, $entity_manager->flushed_removed);
        }
    }
}
