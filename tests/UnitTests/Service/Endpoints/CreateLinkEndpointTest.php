<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\CreateLinkEndpoint;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\CreateLinkEndpoint
 */
final class CreateLinkEndpointTest extends UnitTestCase {
    public function testCreateLinkEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['links' => false];
        $endpoint = new CreateLinkEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'meta' => [
                    'ownerUserId' => 1,
                    'ownerRoleId' => 1,
                    'onOff' => true,
                ],
                'data' => [
                    'name' => 'Test Link',
                    'position' => 3,
                    'url' => 'https://ol-z.ch',
                ],
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403 Kein Zugriff!",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testCreateLinkEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'links' => true,
            'all' => false,
        ];
        $endpoint = new CreateLinkEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'name' => 'Test Link',
                'position' => 3,
                'url' => 'https://ol-z.ch',
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
        $this->assertSame('Test Link', $entity->getName());
        $this->assertSame(3.0, $entity->getPosition());
        $this->assertSame('https://ol-z.ch', $entity->getUrl());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }
}
