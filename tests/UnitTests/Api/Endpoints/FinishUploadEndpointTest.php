<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\FinishUploadEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\FinishUploadEndpoint
 */
final class FinishUploadEndpointTest extends UnitTestCase {
    public function testFinishUploadEndpointUnauthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
                'numberOfParts' => 3,
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
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/valid-but-secret--------.txt', '');
        file_put_contents(__DIR__.'/../../tmp/valid-but-secret-----.txt', '');

        try {
            $endpoint->call([
                'id' => '../valid-but-secret--------.txt',
                'numberOfParts' => 3,
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
                'numberOfParts' => 3,
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

    public function testFinishUploadEndpointInexistentId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);

        $result = $endpoint->call([
            'id' => 'valid-but-inexistent----.txt',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not finish upload. Invalid ID: 'valid-but-inexistent----.txt'.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/valid-but-inexistent----.txt'));
    }

    public function testFinishUploadEndpointMissingFirstPart(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Upload with ID AAAAAAAAAAAAAAAAAAAAAAAA.txt is missing the first part.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertTrue(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0'));
    }

    public function testFinishUploadEndpointNoBase64(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt', '');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0', '');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Upload with ID AAAAAAAAAAAAAAAAAAAAAAAA.txt does not have base64 header.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertTrue(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0'));
    }

    public function testFinishUploadEndpointMissingOtherParts(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt', '');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0', 'data:text/plain;base64,dGVzdA==');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Upload with ID AAAAAAAAAAAAAAAAAAAAAAAA.txt is missing parts 1, 2.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertTrue(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_1'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_2'));
    }

    public function testFinishUploadEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new FinishUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/', 0o777, true);
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt', '');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0', 'data:text/plain;base64,Zmlyc3Q');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_1', 'gc2Vjb25k');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_2', 'IHRoaXJk');

        $result = $endpoint->call([
            'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA.txt',
            'numberOfParts' => 3,
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertTrue(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_0'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_1'));
        $this->assertFalse(is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt_2'));
        $this->assertSame(
            'first second third',
            file_get_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA.txt')
        );
    }
}
