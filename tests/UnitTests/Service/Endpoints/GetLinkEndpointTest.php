<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Entity\Service\Link;
use Olz\Service\Endpoints\GetLinkEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeGetLinkEndpointLinkRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $entry = new Link();
            $entry->setId(12);
            $entry->setName('Fake Link');
            $entry->setPosition(12);
            $entry->setUrl('https://ol-z.ch');
            return $entry;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $entry = new Link();
            $entry->setId(123);
            $entry->setName('Fake Link');
            $entry->setPosition(123);
            $entry->setUrl('https://ol-z.ch');
            return $entry;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $entry = new Link();
            $entry->setId(1234);
            $entry->setName('Fake Link');
            $entry->setPosition(1234);
            $entry->setUrl('https://ol-z.ch');
            $entry->setOnOff(true);
            return $entry;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\GetLinkEndpoint
 */
final class GetLinkEndpointTest extends UnitTestCase {
    public function testGetLinkEndpointIdent(): void {
        $endpoint = new GetLinkEndpoint();
        $this->assertSame('GetLinkEndpoint', $endpoint->getIdent());
    }

    public function testGetLinkEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetLinkEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
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

    public function testGetLinkEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $link_repo = new FakeGetLinkEndpointLinkRepository();
        $entity_manager->repositories[Link::class] = $link_repo;
        $endpoint = new GetLinkEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'name' => 'Fake Link',
                'position' => 12,
                'url' => 'https://ol-z.ch',
            ],
        ], $result);
    }

    public function testGetLinkEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $link_repo = new FakeGetLinkEndpointLinkRepository();
        $entity_manager->repositories[Link::class] = $link_repo;
        $endpoint = new GetLinkEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'name' => 'Fake Link',
                'position' => 123,
                'url' => 'https://ol-z.ch',
            ],
        ], $result);
    }

    public function testGetLinkEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $link_repo = new FakeGetLinkEndpointLinkRepository();
        $entity_manager->repositories[Link::class] = $link_repo;
        $endpoint = new GetLinkEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'name' => 'Fake Link',
                'position' => 1234,
                'url' => 'https://ol-z.ch',
            ],
        ], $result);
    }
}
