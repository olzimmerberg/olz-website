<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\StartUploadEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\StartUploadEndpoint
 */
final class StartUploadEndpointTest extends UnitTestCase {
    public function testStartUploadEndpointIdent(): void {
        $endpoint = new StartUploadEndpoint();
        $this->assertSame('StartUploadEndpoint', $endpoint->getIdent());
    }

    public function testStartUploadEndpointUnauthorized(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new StartUploadEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(['suffix' => null]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testStartUploadEndpointAbort(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new StartUploadEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/AAAAAAAAAAAAAAAAAAAAAAAA', '');

        $result = $endpoint->call(['suffix' => null]);

        $this->assertSame(['status' => 'ERROR', 'id' => null], $result);
        $this->assertSame([
            "INFO Valid user request",
            "ERROR Could not start upload. Finding unique ID failed. Maximum number of loops exceeded.",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testStartUploadEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new StartUploadEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['suffix' => null]);

        $this->assertSame('OK', $result['status']);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9-_]{24}$/', $result['id']);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
    }

    public function testStartUploadEndpointWithSuffix(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new StartUploadEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call(['suffix' => '.pdf']);

        $this->assertSame('OK', $result['status']);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9-_]{24}\.pdf$/', $result['id']);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
    }
}
