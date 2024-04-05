<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\CreateLinkEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\CreateLinkEndpoint
 */
final class CreateLinkEndpointTest extends UnitTestCase {
    public function testCreateLinkEndpointIdent(): void {
        $endpoint = new CreateLinkEndpoint();
        $this->assertSame('CreateLinkEndpoint', $endpoint->getIdent());
    }

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
                "WARNING HTTP error 403",
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
            'status' => 'OK',
            'id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $link = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $link->getId());
        $this->assertSame('Test Link', $link->getName());
        $this->assertSame(3, $link->getPosition());
        $this->assertSame('https://ol-z.ch', $link->getUrl());

        $this->assertSame([
            [$link, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }
}
