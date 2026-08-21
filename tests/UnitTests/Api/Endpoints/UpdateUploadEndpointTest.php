<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\UpdateUploadEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\UpdateUploadEndpoint
 */
final class UpdateUploadEndpointTest extends UnitTestCase {
    public function testUpdateUploadEndpointUnauthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
                'part' => 0,
                'content' => 'ASDF',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403 Kein Zugriff!",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testFinishUploadEndpointInvalidId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/valid-but-secret--------.txt', '');
        file_put_contents(__DIR__.'/../../tmp/valid-but-secret-----.txt', '');

        try {
            $endpoint->call([
                'id' => '../valid-but-secret--------.txt',
                'part' => 0,
                'content' => 'ASDF',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400 Invalid upload ID",
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode());
            $this->assertSame('Invalid upload ID', $err->getMessage());
        }

        $this->resetLogs();
        try {
            $endpoint->call([
                'id' => '../valid-but-secret-----.txt',
                'part' => 0,
                'content' => 'ASDF',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 400 Invalid upload ID",
            ], $this->getLogs());
            $this->assertSame(400, $err->getCode());
            $this->assertSame('Invalid upload ID', $err->getMessage());
        }
    }

    public function testUpdateUploadEndpointInexistentId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);

        $result = $endpoint->call([
            'id' => 'valid-but-inexistent----.txt',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not update upload. Invalid ID: 'valid-but-inexistent----.txt'.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/valid-but-inexistent----.txt'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/valid-but-inexistent----.txt_0'));
    }

    public function testUpdateUploadEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
            'part' => 0,
            'content' => 'ASDF',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertTrue(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0'));
        $this->assertSame('H1', file_get_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0'));
    }
}
