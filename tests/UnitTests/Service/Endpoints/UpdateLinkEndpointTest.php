<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\UpdateLinkEndpoint;
use Olz\Tests\Fake\Entity\Service\FakeLink;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\UpdateLinkEndpoint
 */
final class UpdateLinkEndpointTest extends UnitTestCase {
    public function testUpdateLinkEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateLinkEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
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
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateLinkEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateLinkEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
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
                "NOTICE HTTP error 404",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateLinkEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateLinkEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
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
                "NOTICE HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeLink::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'links'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateLinkEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateLinkEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
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
            'id' => 123,
        ], $result);

        $this->assertSame([
            [FakeLink::empty(), null, null, null, ['ownerUserId' => 1, 'ownerRoleId' => 1, 'onOff' => true], 'links'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $link = $entity_manager->persisted[0];
        $this->assertSame(123, $link->getId());
        $this->assertSame('Test Link', $link->getName());
        $this->assertSame(3, $link->getPosition());
        $this->assertSame('https://ol-z.ch', $link->getUrl());

        $this->assertSame([
            [$link, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);
    }
}
