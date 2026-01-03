<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Anniversary\Endpoints;

use Olz\Anniversary\Endpoints\CreateRunEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Anniversary\Endpoints\CreateRunEndpoint
 */
final class CreateRunEndpointTest extends UnitTestCase {
    public function testCreateRunEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new CreateRunEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'runAt' => null,
                    'distanceMeters' => 3000,
                    'elevationMeters' => 200,
                    'sportType' => 'Test run',
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

    public function testCreateRunEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'all' => false,
        ];
        $endpoint = new CreateRunEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'userId' => FakeUser::maximal()->getId(),
                'runAt' => '2020-08-15 16:27:00',
                'distanceMeters' => 3000,
                'elevationMeters' => 200,
                'sportType' => 'Test run',
                'source' => 'custom',
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
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame(1234, $entity->getUser()?->getId());
        $this->assertSame('2020-08-15 16:27:00', $entity->getRunAt()->format('Y-m-d H:i:s'));
        $this->assertSame(3000, $entity->getDistanceMeters());
        $this->assertSame(200, $entity->getElevationMeters());
        $this->assertSame('Test run', $entity->getSportType());
        $this->assertSame('custom', $entity->getSource());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }

    public function testCreateRunEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::minimal();
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'all' => false,
        ];
        $endpoint = new CreateRunEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'runAt' => null,
                'distanceMeters' => 3000,
                'elevationMeters' => 200,
                'sportType' => null,
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
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame(12, $entity->getUser()?->getId());
        $this->assertSame('2020-03-13 19:30:00', $entity->getRunAt()->format('Y-m-d H:i:s'));
        $this->assertSame(3000, $entity->getDistanceMeters());
        $this->assertSame(200, $entity->getElevationMeters());
        $this->assertNull($entity->getSportType());
        $this->assertSame('manuell', $entity->getSource());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }
}
