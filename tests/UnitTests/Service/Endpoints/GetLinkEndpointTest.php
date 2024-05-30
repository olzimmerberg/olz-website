<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\GetLinkEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

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
                'onOff' => true,
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
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
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
