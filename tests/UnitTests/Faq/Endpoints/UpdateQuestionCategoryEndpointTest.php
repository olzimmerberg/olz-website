<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Faq\Endpoints;

use Olz\Faq\Endpoints\UpdateQuestionCategoryEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategory;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Faq\Endpoints\UpdateQuestionCategoryEndpoint
 */
final class UpdateQuestionCategoryEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'id' => FakeOlzRepository::MAXIMAL_ID,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'position' => 2,
                'name' => 'Test Answer',
            ],
        ];
    }

    public function testUpdateQuestionCategoryEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
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

    public function testUpdateQuestionCategoryEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...$this->getValidInput(),
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
        }
    }

    public function testUpdateQuestionCategoryEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
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

    public function testUpdateQuestionCategoryEndpoint(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['faq' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateQuestionCategoryEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call($this->getValidInput());

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => $id,
        ], $result);

        $this->assertSame([
            [FakeQuestionCategory::maximal(), 'default', 'default', 'role', ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'faq'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame($id, $entity->getId());
        $this->assertSame(2, $entity->getPosition());
        $this->assertSame('Test Answer', $entity->getName());
        $this->assertSame(1, $entity->getOnOff());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $this->assertSame([
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
        $this->assertSame([
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
