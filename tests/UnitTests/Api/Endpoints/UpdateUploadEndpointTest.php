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
    public function testUpdateUploadEndpointIdent(): void {
        $endpoint = new UpdateUploadEndpoint();
        $this->assertSame('UpdateUploadEndpoint', $endpoint->getIdent());
    }

    public function testUpdateUploadEndpointUnauthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 'AAAAAAAAAAAAAAAAAAAAAAAA',
                'part' => 0,
                'content' => 'ASDF',
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateUploadEndpointInvalidId(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

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
        ], $this->getLogs());
        $this->assertSame(false, is_file(__DIR__.'/../../tmp/temp/invalid_0'));
    }

    public function testUpdateUploadEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new UpdateUploadEndpoint();
        $endpoint->runtimeSetup();

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
        ], $this->getLogs());
        $this->assertSame(true, is_file(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
        $this->assertSame('H1', file_get_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA_0'));
    }
}
