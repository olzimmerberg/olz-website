<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Commands\Endpoints;

use Doctrine\DBAL\Driver\Exception as DriverExceptionInterface;
use Doctrine\DBAL\Exception\DriverException;
use Olz\Apps\Commands\Endpoints\ExecuteCommandEndpoint;
use Olz\Common\Utils\WithUtilsCache;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Apps\Commands\Endpoints\ExecuteCommandEndpoint
 */
final class ExecuteCommandEndpointTest extends UnitTestCase {
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
                "NOTICE HTTP error 403 Kein Zugriff!",
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

        $this->assertSame([
            'error' => false,
            'output' => "(no output)",
        ], $result);
        $this->assertSame([
            'fake fake',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testExecuteCommandEndpointError(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'commands' => false,
            'command_fake' => true,
        ];
        WithUtilsCache::get('symfonyUtils')->error = new \Exception('fake');
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['command' => 'fake', 'argv' => 'foo bar']);

        $this->assertSame([
            'error' => true,
            'output' => "\nfake",
        ], $result);
        $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
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

        $this->assertSame([
            'error' => false,
            'output' => "fake output\n",
        ], $result);
        $this->assertSame([
            'fake fake foo bar',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    public function testExecuteCommandEndpointContinuesOnMigrationIssue(): void {
        $commands = ['cache:clear', 'cache:warmup', 'olz:db-migrate', 'olz:db-reset'];
        foreach ($commands as $command) {
            $auth_utils = WithUtilsCache::get('authUtils');
            $auth_utils->has_permission_with_error = $this->createDriverException();
            $symfony_utils = WithUtilsCache::get('symfonyUtils');
            $symfony_utils->commandsCalled = [];
            $endpoint = new ExecuteCommandEndpoint();
            $endpoint->runtimeSetup();

            $result = $endpoint->call(['command' => $command, 'argv' => null]);

            $auth_utils->has_permission_with_error = null;
            $this->assertSame([
                'error' => false,
                'output' => '(no output)',
            ], $result);
            $this->assertSame([
                "{$command} ".escapeshellarg($command),
            ], $symfony_utils->commandsCalled);
        }
    }

    public function testExecuteCommandEndpointThrowsOnMigrationIssue(): void {
        WithUtilsCache::get('authUtils')->has_permission_with_error = $this->createDriverException();
        $endpoint = new ExecuteCommandEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(['command' => 'fake', 'argv' => null]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame(500, $httperr->getCode());
        }
        $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }

    private function createDriverException(): DriverException {
        $driver_exception = new class('Unknown column') extends \Exception implements DriverExceptionInterface {
            public function getSQLState(): string {
                return '42S22';
            }
        };
        return new DriverException($driver_exception, null);
    }
}
