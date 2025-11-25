<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LinkStravaEndpoint;
use Olz\Entity\StravaLink;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LinkStravaEndpoint
 */
final class LinkStravaEndpointTest extends UnitTestCase {
    public function testLinkStravaEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $fake_strava_link = new StravaLink();
        WithUtilsCache::get('stravaUtils')->linkStravaLinkToReturn = $fake_strava_link;
        $endpoint = new LinkStravaEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'code' => 'fake-valid-code',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            ['fake-valid-code'],
        ], WithUtilsCache::get('stravaUtils')->linkStravaCalls);
        $this->assertSame([], $result);
    }

    public function testLinkStravaEndpointError(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        WithUtilsCache::get('stravaUtils')->linkStravaLinkToReturn = null;
        $endpoint = new LinkStravaEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'code' => 'fake-valid-code',
            ]);
            $this->fail('Exception expected.');
        } catch (\Throwable $th) {
            $this->assertSame([
                'INFO Valid user request',
                'NOTICE HTTP error 400',
            ], $this->getLogs());
            $this->assertSame('Ungültige Anfrage!', $th->getMessage());
        }
    }

    public function testLinkStravaEndpointUnauthenticated(): void {
        $endpoint = new LinkStravaEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'code' => 'fake-valid-code',
            ]);
            $this->fail('Exception expected.');
        } catch (\Throwable $th) {
            $this->assertSame([
                'INFO Valid user request',
                'NOTICE HTTP error 403',
            ], $this->getLogs());
            $this->assertSame('Kein Zugriff!', $th->getMessage());
        }
    }
}
