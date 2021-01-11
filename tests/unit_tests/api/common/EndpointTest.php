<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/common/Endpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/fields/Field.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeEndpoint extends Endpoint {
    public $handled_with_input;
    public $handled_with_resource;
    public $handle_with_output;

    public function __construct($resource) {
        $this->resource = $resource;
    }

    public static function getIdent() {
        return 'FakeEndpoint';
    }

    public function getResponseFields() {
        return [
            new Field('output', []),
        ];
    }

    public function getRequestFields() {
        return [
            new Field('input', ['allow_null' => true]),
        ];
    }

    public function getSession() {
        return $this->session;
    }

    public function getServer() {
        return $this->server;
    }

    protected function handle($input) {
        $this->handled_with_input = $input;
        $this->handled_with_resource = $this->resource;
        return $this->handle_with_output;
    }
}

class FakeEndpointWithErrors extends Endpoint {
    public $handle_with_error;
    public $handle_with_output;

    public static function getIdent() {
        return 'FakeEndpointWithErrors';
    }

    public function getResponseFields() {
        return [
            new Field('output', ['allow_null' => false]),
        ];
    }

    public function getRequestFields() {
        return [
            new Field('input', ['allow_null' => false]),
        ];
    }

    protected function handle($input) {
        if ($this->handle_with_error) {
            throw new Exception("Fake Error", 1);
        }
        return $this->handle_with_output;
    }
}

/**
 * @internal
 * @covers \Endpoint
 */
final class EndpointTest extends TestCase {
    public function testFakeEndpoint(): void {
        $memory_session = new MemorySession();
        $fake_server = ['name' => 'fake'];
        $logger = new Logger('EndpointTest');
        $endpoint = new FakeEndpoint('fake_resource');
        global $setup_called;
        $endpoint->setSetupFunction(function ($endpoint) {
            $endpoint->setupCalled = true;
        });
        $endpoint->setup();
        $endpoint->handle_with_output = ['output' => 'test_output'];
        $endpoint->setSession($memory_session);
        $endpoint->setServer($fake_server);
        $endpoint->setLogger($logger);
        $result = $endpoint->call([]);
        $this->assertSame(['input' => null], $endpoint->handled_with_input);
        $this->assertSame(['output' => 'test_output'], $result);
        $this->assertSame('fake_resource', $endpoint->handled_with_resource);
        $this->assertSame($memory_session, $endpoint->getSession());
        $this->assertSame($fake_server, $endpoint->getServer());
        $this->assertSame(true, $endpoint->setupCalled);
    }

    public function testFakeEndpointWithInvalidInput(): void {
        $logger = new Logger('EndpointTest');
        $endpoint = new FakeEndpointWithErrors();
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(400, $err->getCode());
        }
    }

    public function testFakeEndpointWithExecutionError(): void {
        $logger = new Logger('EndpointTest');
        $endpoint = new FakeEndpointWithErrors();
        $endpoint->setLogger($logger);
        $endpoint->handle_with_error = true;
        try {
            $result = $endpoint->call(['input' => 'test']);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(500, $err->getCode());
        }
    }

    public function testFakeEndpointWithInvalidOutput(): void {
        $logger = new Logger('EndpointTest');
        $endpoint = new FakeEndpointWithErrors();
        $endpoint->setLogger($logger);
        $endpoint->handle_with_error = false;
        $endpoint->handle_with_output = [];
        try {
            $result = $endpoint->call(['input' => 'test']);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(500, $err->getCode());
        }
    }

    public function testFakeEndpointWithoutAnyErrors(): void {
        $logger = new Logger('EndpointTest');
        $endpoint = new FakeEndpointWithErrors();
        $endpoint->setLogger($logger);
        $endpoint->handle_with_error = false;
        $endpoint->handle_with_output = ['output' => 'test'];
        $result = $endpoint->call(['input' => 'test']);
        $this->assertSame(['output' => 'test'], $result);
    }
}
