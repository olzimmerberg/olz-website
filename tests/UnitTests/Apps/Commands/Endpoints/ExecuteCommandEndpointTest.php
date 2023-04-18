<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Commands\Endpoints;

use Olz\Apps\Commands\Endpoints\ExecuteCommandEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Commands\Endpoints\ExecuteCommandEndpoint
 */
final class ExecuteCommandEndpointTest extends UnitTestCase {
    public function testExecuteCommandEndpointIdent(): void {
        $endpoint = new ExecuteCommandEndpoint();
        $this->assertSame('ExecuteCommandEndpoint', $endpoint->getIdent());
    }

    public function testExecuteCommandEndpointNoAccess(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = [
            'commands' => false,
            'command_fake' => false,
        ];
        $logger = Fake\FakeLogger::create();
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        try {
            $result = $endpoint->call(['command' => 'fake', 'argv' => 'foo bar']);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testExecuteCommandEndpointNoOutput(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = [
            'commands' => true,
            'command_fake' => false,
        ];
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['command' => 'fake', 'argv' => null]);

        $this->assertSame(['output' => "(no output)"], $result);
        $this->assertSame([
            ['fake', 'fake'],
        ], $symfony_utils->commandsCalled);
    }

    public function testExecuteCommandEndpoint(): void {
        $auth_utils = new Fake\FakeAuthUtils();
        $auth_utils->has_permission_by_query = [
            'commands' => false,
            'command_fake' => true,
        ];
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $symfony_utils->output = 'fake output';
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setSymfonyUtils($symfony_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call(['command' => 'fake', 'argv' => 'foo bar']);

        $this->assertSame(['output' => "fake output\n"], $result);
        $this->assertSame([
            ['fake', 'fake foo bar'],
        ], $symfony_utils->commandsCalled);
    }
}
