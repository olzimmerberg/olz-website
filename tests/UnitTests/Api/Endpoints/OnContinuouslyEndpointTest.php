<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\OnContinuouslyEndpoint;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\OnContinuouslyEndpoint
 */
final class OnContinuouslyEndpointTest extends UnitTestCase {
    public function testOnContinuouslyEndpointParseInput(): void {
        $get_params = ['authenticityCode' => 'some-token'];
        $request = new Request($get_params);
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->runtimeSetup();
        $parsed_input = $endpoint->parseInput($request);
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnContinuouslyEndpointWrongToken(): void {
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setEnvUtils(new FakeEnvUtils());

        try {
            $result = $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
            $this->assertSame([], WithUtilsCache::get('symfonyUtils')->commandsCalled);
        }
    }

    public function testOnContinuouslyEndpoint(): void {
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setEnvUtils(new FakeEnvUtils());

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([], $result);
        $this->assertSame([
            'olz:on-continuously ',
        ], WithUtilsCache::get('symfonyUtils')->commandsCalled);
    }
}
