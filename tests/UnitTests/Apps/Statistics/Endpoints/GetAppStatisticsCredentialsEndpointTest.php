<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Statistics\Endpoints;

use Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint
 */
final class GetAppStatisticsCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppStatisticsCredentialsEndpointIdent(): void {
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        $this->assertSame('GetAppStatisticsCredentialsEndpoint', $endpoint->getIdent());
    }

    public function testGetAppStatisticsCredentialsEndpoint(): void {
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::adminUser();
        $endpoint->setup();

        $result = $endpoint->call([]);

        $this->assertSame([
            'username' => 'fake',
            'password' => 'qwer',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Statistics credentials access by admin.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testGetAppStatisticsCredentialsEndpointNotAuthorized(): void {
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
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

    public function testGetAppStatisticsCredentialsEndpointNotAuthenticated(): void {
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
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
