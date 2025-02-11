<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Faq\Endpoints;

use Olz\Faq\Endpoints\DeleteQuestionCategoryEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategory;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Faq\Endpoints\DeleteQuestionCategoryEndpoint
 */
final class DeleteQuestionCategoryEndpointTest extends UnitTestCase {
    public function testDeleteQuestionCategoryEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new DeleteQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::MINIMAL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testDeleteQuestionCategoryEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new DeleteQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::MINIMAL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testDeleteQuestionCategoryEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => FakeOlzRepository::MINIMAL_ID,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([], $result);

        $this->assertSame([
            [FakeQuestionCategory::minimal(), null, null, null, null, 'faq'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $download = $entity_manager->persisted[0];
        $this->assertSame(FakeOlzRepository::MINIMAL_ID, $download->getId());
        $this->assertSame(0, $download->getOnOff());
    }

    public function testDeleteQuestionCategoryEndpointInexistent(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new DeleteQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::NULL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );
            $this->assertSame(404, $err->getCode());
            $entity_manager = WithUtilsCache::get('entityManager');
            $this->assertCount(0, $entity_manager->removed);
            $this->assertCount(0, $entity_manager->flushed_removed);
        }
    }
}
