<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Snippets\Endpoints\UpdateSnippetEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Snippets\Endpoints\UpdateSnippetEndpoint
 */
final class UpdateSnippetEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'text' => 'Updated text',
            'imageIds' => ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
            'fileIds' => ['uploaded_file1.pdf', 'uploaded_file2.txt'],
        ],
    ];

    public function testUpdateSnippetEndpointIdent(): void {
        $endpoint = new UpdateSnippetEndpoint();
        $this->assertSame('UpdateSnippetEndpoint', $endpoint->getIdent());
    }

    public function testUpdateSnippetEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['snippet_123' => false];
        $endpoint = new UpdateSnippetEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call(self::VALID_INPUT);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateSnippetEndpointNoEntry(): void {
        $id = 9999;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateSnippetEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageA.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageB.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file2.txt', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/snippets/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/snippets/');

        $result = $endpoint->call([
            ...self::VALID_INPUT,
            'id' => $id,
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => $id,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(2, count($entity_manager->persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $snippet = $entity_manager->persisted[0];
        $this->assertSame($id, $snippet->getId());
        $this->assertSame('Updated text', $snippet->getText());

        $this->assertSame([
            [$snippet, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/snippets/{$id}/img/",
            ],
            [
                ['uploaded_file1.pdf', 'uploaded_file2.txt'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/files/snippets/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }

    public function testUpdateSnippetEndpoint(): void {
        $id = 123;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateSnippetEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageA.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageB.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file2.txt', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/snippets/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/snippets/');

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => $id,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $snippet = $entity_manager->persisted[0];
        $this->assertSame($id, $snippet->getId());
        $this->assertSame('Updated text', $snippet->getText());

        $this->assertSame([
            [$snippet, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/snippets/{$id}/img/",
            ],
            [
                ['uploaded_file1.pdf', 'uploaded_file2.txt'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/files/snippets/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
