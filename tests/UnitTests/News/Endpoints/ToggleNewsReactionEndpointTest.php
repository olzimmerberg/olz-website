<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\News\Endpoints;

use Olz\News\Endpoints\ToggleNewsReactionEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\News\Endpoints\ToggleNewsReactionEndpoint
 */
final class ToggleNewsReactionEndpointTest extends UnitTestCase {
    public function testToggleNewsReactionEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        WithUtilsCache::get('authUtils')->current_user = null;
        $endpoint = new ToggleNewsReactionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'newsEntryId' => 123,
                'emoji' => '👍',
                'action' => 'toggle',
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

    public function testToggleNewsReactionEndpointTurnOn(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::minimal();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::minimal()->getId(),
        ];
        $endpoint = new ToggleNewsReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'newsEntryId' => 123,
            'emoji' => '👍',
            'action' => 'on',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => [
                'userId' => 12,
                'name' => 'Required Non-empty',
                'emoji' => '👍',
            ],
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->removed);
        $this->assertCount(0, $entity_manager->flushed_removed);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame(12, $entity->getUser()?->getId());
        $this->assertSame(123, $entity->getNewsEntry()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }

    public function testToggleNewsReactionEndpointTurnOff(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::empty();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::empty()->getId(),
        ];
        $endpoint = new ToggleNewsReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'newsEntryId' => 123,
            'emoji' => '⭕',
            'action' => 'off',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => null,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->persisted);
        $this->assertCount(0, $entity_manager->flushed_persisted);
        $this->assertCount(1, $entity_manager->removed);
        $this->assertCount(1, $entity_manager->flushed_removed);
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
        $entity = $entity_manager->removed[0];
        $this->assertSame(123, $entity->getId());
        $this->assertSame(123, $entity->getUser()?->getId());
        $this->assertSame(123, $entity->getNewsEntry()?->getId());
        $this->assertSame('⭕', $entity->getEmoji());
    }

    public function testToggleNewsReactionEndpointToggleOn(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::child1User();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::parentUser()->getId(),
        ];
        $endpoint = new ToggleNewsReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'newsEntryId' => 123,
            'emoji' => '👍',
            'action' => 'toggle',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => [
                'userId' => 5,
                'name' => 'Kind Eins',
                'emoji' => '👍',
            ],
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->removed);
        $this->assertCount(0, $entity_manager->flushed_removed);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame(5, $entity->getUser()?->getId());
        $this->assertSame(123, $entity->getNewsEntry()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }

    public function testToggleNewsReactionEndpointTurnOnForChildUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::child1User();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::parentUser()->getId(),
        ];
        $endpoint = new ToggleNewsReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'userId' => FakeUser::child1User()->getId(),
            'newsEntryId' => 123,
            'emoji' => '👍',
            'action' => 'toggle',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'result' => [
                'userId' => 5,
                'name' => 'Kind Eins',
                'emoji' => '👍',
            ],
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->removed);
        $this->assertCount(0, $entity_manager->flushed_removed);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame(5, $entity->getUser()?->getId());
        $this->assertSame(123, $entity->getNewsEntry()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }

    public function testToggleNewsReactionEndpointTurnOnForOtherUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::defaultUser()->getId(),
        ];
        $endpoint = new ToggleNewsReactionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'userId' => FakeUser::adminUser()->getId(),
                'newsEntryId' => 123,
                'emoji' => '👍',
                'action' => 'toggle',
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
}
