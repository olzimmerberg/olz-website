<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Monitoring\Endpoints;

use Olz\Apps\Monitoring\Endpoints\GetAppMonitoringCredentialsEndpoint;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
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
        $logger = FakeLogger::create();
        $endpoint = new GetAppMonitoringCredentialsEndpoint();
        $env_utils = new FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

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
        $logger = FakeLogger::create();
        $endpoint = new GetAppMonitoringCredentialsEndpoint();
        $env_utils = new FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

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

    public function testGetAppMonitoringCredentialsEndpointNotAuthenticated(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppMonitoringCredentialsEndpoint();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setLogger($logger);

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
