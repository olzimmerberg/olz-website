<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\GoogleSearch\Endpoints;

use Olz\Apps\GoogleSearch\Endpoints\GetAppGoogleSearchCredentialsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
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
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['all' => true];
        $auth_utils->current_user = Fake\FakeUsers::adminUser();
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
        $env_utils = new Fake\FakeEnvUtils();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

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
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['all' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetAppGoogleSearchCredentialsEndpoint();
        $env_utils = new Fake\FakeEnvUtils();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
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
