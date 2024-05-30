<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetRegistrationEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Apps\Anmelden\Endpoints\GetRegistrationEndpoint
 */
final class GetRegistrationEndpointTest extends UnitTestCase {
    public function testGetRegistrationEndpointIdent(): void {
        $endpoint = new GetRegistrationEndpoint();
        $this->assertSame('GetRegistrationEndpoint', $endpoint->getIdent());
    }

    public function testGetRegistrationEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetRegistrationEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 'Registration:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ]);

        $this->assertSame([
            'id' => 'Registration:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
            'meta' => [
                'ownerUserId' => 2,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'title' => 'Test title',
                'description' => '',
                'infos' => [
                    [
                        'type' => 'string',
                        'isOptional' => false,
                        'title' => 'Test Info 1',
                        'description' => '',
                        'options' => null,
                    ],
                    [
                        'type' => 'string',
                        'isOptional' => true,
                        'title' => 'Test Info 2',
                        'description' => '',
                        'options' => null,
                    ],
                ],
                'opensAt' => '2020-03-13 15:00:00',
                'closesAt' => '2020-03-16 09:00:00',
            ],
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(0, $entity_manager->persisted);
        $this->assertCount(0, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
