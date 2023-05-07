<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Commands\Endpoints;

use Olz\Apps\Commands\Endpoints\ExecuteCommandEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
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
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'commands' => false,
            'command_fake' => false,
        ];
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->runtimeSetup();

        try {
            $result = $endpoint->call(['command' => 'fake', 'argv' => 'foo bar']);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Kein Zugriff!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
        }
    }

    public function testExecuteCommandEndpointNoOutput(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'commands' => true,
            'command_fake' => false,
        ];
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['command' => 'fake', 'argv' => null]);

        $this->assertSame(['output' => "(no output)"], $result);
        $this->assertSame([
            ['fake', 'fake'],
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testExecuteCommandEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'commands' => false,
            'command_fake' => true,
        ];
        WithUtilsCache::get('symfonyUtils')->output = 'fake output';
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['command' => 'fake', 'argv' => 'foo bar']);

        $this->assertSame(['output' => "fake output\n"], $result);
        $this->assertSame([
            ['fake', 'fake foo bar'],
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
