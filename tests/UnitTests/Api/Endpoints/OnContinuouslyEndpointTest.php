<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\OnContinuouslyEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\OnContinuouslyEndpoint
 */
final class OnContinuouslyEndpointTest extends UnitTestCase {
    public function testOnContinuouslyEndpointIdent(): void {
        $endpoint = new OnContinuouslyEndpoint();
        $this->assertSame('OnContinuouslyEndpoint', $endpoint->getIdent());
    }

    public function testOnContinuouslyEndpointParseInput(): void {
        $get_params = ['authenticityCode' => 'some-token'];
        $request = new Request($get_params);
        $endpoint = new OnContinuouslyEndpoint();
        $parsed_input = $endpoint->parseInput($request);
        $this->assertSame([
            'authenticityCode' => 'some-token',
        ], $parsed_input);
    }

    public function testOnContinuouslyEndpointWrongToken(): void {
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEnvUtils(new Fake\FakeEnvUtils());
        $endpoint->setSymfonyUtils($symfony_utils);

        try {
            $result = $endpoint->call([
                'authenticityCode' => 'wrong-token',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                'INFO Valid user request',
                'WARNING HTTP error 403',
            ], $logger->handler->getPrettyRecords());
            $this->assertSame(403, $err->getCode());
            $this->assertSame([], $symfony_utils->commandsCalled);
        }
    }

    public function testOnContinuouslyEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $symfony_utils = new Fake\FakeSymfonyUtils();
        $endpoint = new OnContinuouslyEndpoint();
        $endpoint->setLog($logger);
        $endpoint->setEnvUtils(new Fake\FakeEnvUtils());
        $endpoint->setSymfonyUtils($symfony_utils);

        $result = $endpoint->call([
            'authenticityCode' => 'some-token',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $logger->handler->getPrettyRecords());
        $this->assertSame([], $result);
        $this->assertSame([
            ['olz:on-continuously', ''],
        ], $symfony_utils->commandsCalled);
    }
}
