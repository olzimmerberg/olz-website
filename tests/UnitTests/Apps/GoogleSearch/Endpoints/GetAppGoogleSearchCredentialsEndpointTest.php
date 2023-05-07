<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\GoogleSearch\Endpoints;

use Olz\Apps\GoogleSearch\Endpoints\GetAppGoogleSearchCredentialsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\GoogleSearch\Endpoints\GetAppGoogleSearchCredentialsEndpoint
 */
final class GetAppGoogleSearchCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppGoogleSearchCredentialsEndpointIdent(): void {
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
        $this->assertSame('GetAppGoogleSearchCredentialsEndpoint', $endpoint->getIdent());
    }

    public function testGetAppGoogleSearchCredentialsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();

        $result = $endpoint->call([]);

        $this->assertSame([
            'username' => 'fake@gmail.com',
            'password' => 'zxcv',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO GoogleSearch credentials access by admin.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testGetAppGoogleSearchCredentialsEndpointNotAuthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();

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
