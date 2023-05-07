<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Youtube\Endpoints;

use Olz\Apps\Youtube\Endpoints\GetAppYoutubeCredentialsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Youtube\Endpoints\GetAppYoutubeCredentialsEndpoint
 */
final class GetAppYoutubeCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppYoutubeCredentialsEndpointIdent(): void {
        $endpoint = new GetAppYoutubeCredentialsEndpoint();
        $this->assertSame('GetAppYoutubeCredentialsEndpoint', $endpoint->getIdent());
    }

    public function testGetAppYoutubeCredentialsEndpoint(): void {
        $endpoint = new GetAppYoutubeCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();

        $result = $endpoint->call([]);

        $this->assertSame([
            'username' => 'fake@gmail.com',
            'password' => 'zxcv',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Youtube credentials access by admin.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testGetAppYoutubeCredentialsEndpointNotAuthorized(): void {
        $endpoint = new GetAppYoutubeCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];

        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
        }
    }
}
