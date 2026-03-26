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
        $this->assertSame([], $result);
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
        $this->assertSame([], $result);
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
        WithUtilsCache::get('authUtils')->current_user = FakeUser::maximal();
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
        $this->assertSame([], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->removed);
        $this->assertCount(0, $entity_manager->flushed_removed);
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame(1234, $entity->getUser()?->getId());
        $this->assertSame(123, $entity->getTermin()?->getId());
        $this->assertSame('👍', $entity->getEmoji());
    }
}
