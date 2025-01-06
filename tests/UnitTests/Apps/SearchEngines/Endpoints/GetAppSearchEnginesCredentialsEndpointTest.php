<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\SearchEngines\Endpoints;

use Olz\Apps\SearchEngines\Endpoints\GetAppSearchEnginesCredentialsEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\SearchEngines\Endpoints\GetAppSearchEnginesCredentialsEndpoint
 */
final class GetAppSearchEnginesCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppSearchEnginesCredentialsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint = new GetAppSearchEnginesCredentialsEndpoint();
        $endpoint->setup();

        $result = $endpoint->call([]);

        $this->assertSame([
            'username' => 'fake@gmail.com',
            'password' => 'zxcv',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO SearchEngines credentials access by admin.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testGetAppSearchEnginesCredentialsEndpointNotAuthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $endpoint = new GetAppSearchEnginesCredentialsEndpoint();
        $endpoint->setup();

        try {
            $endpoint->call([]);
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
