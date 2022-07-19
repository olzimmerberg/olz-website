<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Results\Endpoints;

use Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 * @covers \Olz\Apps\Statistics\Endpoints\GetAppStatisticsCredentialsEndpoint
 */
final class GetAppStatisticsCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppStatisticsCredentialsEndpointIdent(): void {
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
        $this->assertSame('GetAppStatisticsCredentialsEndpoint', $endpoint->getIdent());
    }

    public function testGetAppStatisticsCredentialsEndpoint(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
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
            'password' => 'qwer',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Statistics credentials access by admin.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testGetAppStatisticsCredentialsEndpointNotAuthorized(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
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
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testGetAppStatisticsCredentialsEndpointNotAuthenticated(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppStatisticsCredentialsEndpoint();
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
            ], $logger->handler->getPrettyRecords());
        }
    }
}
