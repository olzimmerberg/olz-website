<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\GoogleSearch\Endpoints;

use Olz\Apps\GoogleSearch\Endpoints\GetAppGoogleSearchCredentialsEndpoint;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 * @covers \Olz\Apps\GoogleSearch\Endpoints\GetAppGoogleSearchCredentialsEndpoint
 */
final class GetAppGoogleSearchCredentialsEndpointTest extends UnitTestCase {
    public function testGetAppGoogleSearchCredentialsEndpointIdent(): void {
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
        $this->assertSame('GetAppGoogleSearchCredentialsEndpoint', $endpoint->getIdent());
    }

    public function testGetAppGoogleSearchCredentialsEndpoint(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
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
            "INFO GoogleSearch credentials access by admin.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testGetAppGoogleSearchCredentialsEndpointNotAuthorized(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
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

    public function testGetAppGoogleSearchCredentialsEndpointNotAuthenticated(): void {
        $logger = FakeLogger::create();
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
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
