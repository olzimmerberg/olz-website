<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Statistics\Endpoints;

use Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
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
        $env_utils = new Fake\FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'all',
            'root' => '',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
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
        $env_utils = new Fake\FakeEnvUtils();
        $session = new MemorySession();
        $session->session_storage = [
            'auth' => 'ftp',
            'root' => 'karten',
            'user' => 'admin',
        ];
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setSession($session);
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
        $session = new MemorySession();
        $endpoint->setSession($session);
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
