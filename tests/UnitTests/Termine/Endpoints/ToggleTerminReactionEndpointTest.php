<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Termine\Endpoints;

use Olz\Termine\Endpoints\ToggleTerminReactionEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Termine\Endpoints\ToggleTerminReactionEndpoint
 */
final class ToggleTerminReactionEndpointTest extends UnitTestCase {
    public function testToggleTerminReactionEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        WithUtilsCache::get('authUtils')->current_user = null;
        $endpoint = new ToggleTerminReactionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'terminId' => 123,
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

    public function testToggleTerminReactionEndpointTurnOn(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::minimal();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::minimal()->getId(),
        ];
        $endpoint = new ToggleTerminReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'terminId' => 123,
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
        $this->assertSame(123, $entity->getTermin()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }

    public function testToggleTerminReactionEndpointTurnOff(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::empty();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::empty()->getId(),
        ];
        $endpoint = new ToggleTerminReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'terminId' => 123,
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
        $this->assertSame(123, $entity->getTermin()?->getId());
        $this->assertSame('⭕', $entity->getEmoji());
    }

    public function testToggleTerminReactionEndpointToggleOn(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::child1User();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::parentUser()->getId(),
        ];
        $endpoint = new ToggleTerminReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'terminId' => 123,
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
        $this->assertSame(123, $entity->getTermin()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }

    public function testToggleTerminReactionEndpointTurnOnForChildUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::child1User();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::parentUser()->getId(),
        ];
        $endpoint = new ToggleTerminReactionEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'userId' => FakeUser::child1User()->getId(),
            'terminId' => 123,
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
        $this->assertSame(123, $entity->getTermin()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }

    public function testToggleTerminReactionEndpointTurnOnForOtherUser(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('session')->session_storage = [
            'auth_user_id' => FakeUser::defaultUser()->getId(),
        ];
        $endpoint = new ToggleTerminReactionEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'userId' => FakeUser::adminUser()->getId(),
                'terminId' => 123,
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
