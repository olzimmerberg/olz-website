<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/api/client/ApiGenerator.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeApiGeneratorEndpoint extends Endpoint {
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

class FakeApi {
    public $endpoints = [];

    public function __construct() {
        $this->endpoints = [
            'fakeEndpoint' => function () {
                $endpoint = new FakeApiGeneratorEndpoint('fake-resource');
                $endpoint->setSetupFunction(function () {
                });
                return $endpoint;
            },
        ];
    }
}

/**
 * @internal
 * @covers \ApiGenerator
 */
final class ApiGeneratorTest extends UnitTestCase {
    public function testApiGenerator(): void {
        $generator = new ApiGenerator();
        $fake_api = new FakeApi();
        $expected_output = <<<'ZZZZZZZZZZ'
/** ### This file is auto-generated, modifying is futile! ### */

// eslint-disable-next-line no-shadow
export enum FakeApiEndpoint {
    fakeEndpoint = 'fakeEndpoint',
}

type FakeApiEndpointMapping = {[key in FakeApiEndpoint]: any};

export interface FakeApiRequests extends FakeApiEndpointMapping {
    fakeEndpoint: {
        input: any,
    },
}

export interface FakeApiResponses extends FakeApiEndpointMapping {
    fakeEndpoint: {
        output: any,
    },
}


ZZZZZZZZZZ;
        $this->assertSame($expected_output, $generator->generate($fake_api, 'FakeApi'));
    }
}
