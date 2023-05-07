<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\UpdateUploadEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\UploadUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\UpdateUploadEndpoint
 */
final class UpdateUploadEndpointTest extends UnitTestCase {
    public function testUpdateUploadEndpointIdent(): void {
        $endpoint = new UpdateUploadEndpoint();
        $this->assertSame('UpdateUploadEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUploadEndpointUnauthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testUpdateUploadEndpointInvalidId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $env_utils = new Fake\FakeEnvUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);

        $result = $endpoint->call([
            'id' => 'invalid',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not update upload. Invalid ID: 'invalid'.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/invalid_0'));
    }

    public function testUpdateUploadEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $env_utils = new Fake\FakeEnvUtils();
        $upload_utils = new UploadUtils();
        $logger = Fake\FakeLogger::create();
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setUploadUtils($upload_utils);
        $endpoint->setLog($logger);

        mkdir(__DIR__.'/../../tmp/temp/', 0777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
        $this->assertSame('H1', file_get_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
    }
}
