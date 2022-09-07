<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Youtube\Endpoints;

use Olz\Apps\Youtube\Endpoints\GetAppYoutubeCredentialsEndpoint;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
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
        $logger = FakeLogger::create();
        $endpoint = new GetAppYoutubeCredentialsEndpoint();
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
            'username' => 'fake@gmail.com',
            'password' => 'zxcv',
        ], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Youtube credentials access by admin.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testGetAppYoutubeCredentialsEndpointNotAuthorized(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppYoutubeCredentialsEndpoint();
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

    public function testGetAppYoutubeCredentialsEndpointNotAuthenticated(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppYoutubeCredentialsEndpoint();
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