<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Service\Endpoints;

use Olz\Service\Endpoints\EditLinkEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Service\Endpoints\EditLinkEndpoint
 */
final class EditLinkEndpointTest extends UnitTestCase {
    public function testEditLinkEndpointIdent(): void {
        $endpoint = new EditLinkEndpoint();
        $this->assertSame('EditLinkEndpoint', $endpoint->getIdent());
    }

    public function testEditLinkEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new EditLinkEndpoint();
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

    public function testEditLinkEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new EditLinkEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testEditLinkEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditLinkEndpoint();
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

    public function testEditLinkEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditLinkEndpoint();
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

    public function testEditLinkEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditLinkEndpoint();
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

    public function testEditLinkEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditLinkEndpoint();
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
