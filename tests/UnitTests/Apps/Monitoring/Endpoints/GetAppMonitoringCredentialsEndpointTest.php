<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Monitoring\Endpoints;

use Olz\Apps\Monitoring\Endpoints\GetAppMonitoringCredentialsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Monitoring\Endpoints\GetAppMonitoringCredentialsEndpoint
 */
final class GetAppMonitoringCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppMonitoringCredentialsEndpointIdent(): void {
        $endpoint = new GetAppMonitoringCredentialsEndpoint();
        $this->assertSame('GetAppMonitoringCredentialsEndpoint', $endpoint->getIdent());
    }

    public function testGetAppMonitoringCredentialsEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::adminUser();
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppMonitoringCredentialsEndpoint();
        $endpoint->setLog($logger);

        $result = $endpoint->call([]);

        $this->assertSame([
            'username' => 'fake',
            'password' => 'asdf',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Monitoring credentials access by admin.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testGetAppMonitoringCredentialsEndpointNotAuthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppMonitoringCredentialsEndpoint();
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
