<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\FinishUploadEndpoint;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\FinishUploadEndpoint
 */
final class FinishUploadEndpointTest extends UnitTestCase {
    public function testFinishUploadEndpointIdent(): void {
        $endpoint = new FinishUploadEndpoint();
        $this->assertSame('FinishUploadEndpoint', $endpoint->getIdent());
    }

    public function testFinishUploadEndpointUnauthorized(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $logger = FakeLogger::create();
        $endpoint = new FinishUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testFinishUploadEndpointInvalidId(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new FinishUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);

        $result = $endpoint->call([
            'id' => 'invalid',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not finish upload. Invalid ID: 'invalid'.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA'));
    }

    public function testFinishUploadEndpointMissingFirstPart(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new FinishUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Upload with ID AAAAAAAAAAAAAAAAAAAAAAAA is missing the first part.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
    }

    public function testFinishUploadEndpointNoBase64(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new FinishUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Upload with ID AAAAAAAAAAAAAAAAAAAAAAAA does not have base64 header.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
    }

    public function testFinishUploadEndpointMissingOtherParts(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new FinishUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0', 'data:text/plain;base64,dGVzdA==');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Upload with ID AAAAAAAAAAAAAAAAAAAAAAAA is missing parts 1, 2.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_1'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_2'));
    }

    public function testFinishUploadEndpoint(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new FinishUploadEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0', 'data:text/plain;base64,Zmlyc3Q');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_1', 'gc2Vjb25k');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_2', 'IHRoaXJk');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_1'));
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_2'));
        $this->assertSame(
            'first second third',
            file_get_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA')
        );
    }
}
