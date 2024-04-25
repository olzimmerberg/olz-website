<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Snippets\Endpoints;

use Olz\Snippets\Endpoints\EditSnippetEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Snippets\Endpoints\EditSnippetEndpoint
 */
final class EditSnippetEndpointTest extends UnitTestCase {
    public function testEditSnippetEndpointIdent(): void {
        $endpoint = new EditSnippetEndpoint();
        $this->assertSame('EditSnippetEndpoint', $endpoint->getIdent());
    }

    public function testEditSnippetEndpointNoAccess(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => false];
        $endpoint = new EditSnippetEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => $id,
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

    public function testEditSnippetEndpointNoSuchEntity(): void {
        $id = FakeOlzRepository::NULL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditSnippetEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
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

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $snippet = $entity_manager->persisted[0];
        $this->assertSame($id, $snippet->getId());
        $this->assertSame('', $snippet->getText());

        $this->assertSame([
            [$snippet, 1, null, null],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $this->assertSame([], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }

    public function testEditSnippetEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditSnippetEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
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

    public function testEditSnippetEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditSnippetEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
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

    public function testEditSnippetEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ["snippet_{$id}" => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditSnippetEndpoint();
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
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'id' => $id,
            'meta' => [
                'ownerUserId' => null,
                'ownerRoleId' => null,
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
