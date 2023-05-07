<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Statistics\Endpoints;

use Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint;
use Olz\Tests\Fake;
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
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();
        $endpoint->setLog($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'username' => 'fake',
            'password' => 'qwer',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Statistics credentials access by admin.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testGetAppStatisticsCredentialsEndpointNotAuthorized(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testGetAppStatisticsCredentialsEndpointNotAuthenticated(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
        }
    }
}
