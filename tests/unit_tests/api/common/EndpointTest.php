<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/api/common/Endpoint.php';
require_once __DIR__.'/../../../../src/fields/Field.php';

class FakeEndpoint extends Endpoint {
    public $handled_with_input;
    public $handled_with_resource;
    public $handle_with_output;

    public function __construct($resource) {
        $this->resource = $resource;
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

    protected function handle($input) {
        $this->handled_with_input = $input;
        $this->handled_with_resource = $this->resource;
        return $this->handle_with_output;
    }
}

/**
 * @internal
 * @covers \Endpoint
 */
final class EndpointTest extends TestCase {
    public function testFakeEndpoint(): void {
        $endpoint = new FakeEndpoint('fake_resource');
        $endpoint->handle_with_output = ['output' => 'test_output'];
        $result = $endpoint->call([]);
        $this->assertSame(['input' => null], $endpoint->handled_with_input);
        $this->assertSame(['output' => 'test_output'], $result);
        $this->assertSame('fake_resource', $endpoint->handled_with_resource);
    }
}
