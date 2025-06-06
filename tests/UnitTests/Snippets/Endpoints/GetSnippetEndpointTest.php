<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Snippets\Endpoints;

use Olz\Snippets\Endpoints\GetSnippetEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Snippets\Endpoints\GetSnippetEndpoint
 */
final class GetSnippetEndpointTest extends UnitTestCase {
    public function testGetSnippetEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetSnippetEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testGetSnippetEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
            'snippet_9999' => false, // if we had the permission, the entity would be auto-created.
        ];
        $endpoint = new GetSnippetEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testGetSnippetEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetSnippetEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 12,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 12,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'text' => '',
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetSnippetEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetSnippetEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => 123,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 123,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
                'onOff' => false,
            ],
            'data' => [
                'text' => '',
                'imageIds' => [],
                'fileIds' => [],
            ],
        ], $result);
    }

    public function testGetSnippetEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetSnippetEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/snippets/');
        mkdir(__DIR__."/../../tmp/files/snippets/{$id}/");
        file_put_contents(__DIR__."/../../tmp/files/snippets/{$id}/aaaaaaaaaaaaaaaaaaaaaaaa.svg", '');
        file_put_contents(__DIR__."/../../tmp/files/snippets/{$id}/file___________________1.pdf", '');
        file_put_contents(__DIR__."/../../tmp/files/snippets/{$id}/file___________________2.txt", '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/snippets/');
        mkdir(__DIR__."/../../tmp/img/snippets/{$id}/");
        mkdir(__DIR__."/../../tmp/img/snippets/{$id}/img");
        file_put_contents(__DIR__."/../../tmp/img/snippets/{$id}/img/picture________________A.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/snippets/{$id}/img/picture________________B.jpg", '');

        $result = $endpoint->call([
            'id' => 1234,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => 1234,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'text' => 'test-text',
                'imageIds' => ['picture________________A.jpg', 'picture________________B.jpg'],
                'fileIds' => ['aaaaaaaaaaaaaaaaaaaaaaaa.svg', 'file___________________1.pdf', 'file___________________2.txt'],
            ],
        ], $result);
    }
}
