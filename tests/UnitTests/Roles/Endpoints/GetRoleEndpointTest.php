<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\GetRoleEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\GetRoleEndpoint
 */
final class GetRoleEndpointTest extends UnitTestCase {
    public function testGetRoleEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => false];
        $endpoint = new GetRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => FakeOlzRepository::MINIMAL_ID,
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

    public function testGetRoleEndpointMinimal(): void {
        $id = FakeOlzRepository::MINIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__."/../../tmp/files/roles/{$id}/");
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');
        mkdir(__DIR__."/../../tmp/img/roles/{$id}/");
        mkdir(__DIR__."/../../tmp/img/roles/{$id}/img/");

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
                'username' => '-',
                'name' => '-',
                'title' => null,
                'description' => '',
                'guide' => '',
                'imageIds' => [],
                'fileIds' => [],
                'parentRole' => null,
                'indexWithinParent' => null,
                'featuredIndex' => null,
                'canHaveChildRoles' => false,
            ],
        ], $result);
    }

    public function testGetRoleEndpointEmpty(): void {
        $id = FakeOlzRepository::EMPTY_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__."/../../tmp/files/roles/{$id}/");
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');
        mkdir(__DIR__."/../../tmp/img/roles/{$id}/");
        mkdir(__DIR__."/../../tmp/img/roles/{$id}/img/");

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
                'username' => '-',
                'name' => '-',
                'title' => null,
                'description' => '',
                'guide' => '',
                'imageIds' => [],
                'fileIds' => [],
                'parentRole' => null,
                'indexWithinParent' => null,
                'featuredIndex' => null,
                'canHaveChildRoles' => false,
            ],
        ], $result);
    }

    public function testGetRoleEndpointMaximal(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new GetRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__."/../../tmp/files/roles/{$id}/");
        file_put_contents(__DIR__."/../../tmp/files/roles/{$id}/file___________________1.pdf", '');
        file_put_contents(__DIR__."/../../tmp/files/roles/{$id}/file___________________2.txt", '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');
        mkdir(__DIR__."/../../tmp/img/roles/{$id}/");
        mkdir(__DIR__."/../../tmp/img/roles/{$id}/img");
        file_put_contents(__DIR__."/../../tmp/img/roles/{$id}/img/picture________________A.jpg", '');
        file_put_contents(__DIR__."/../../tmp/img/roles/{$id}/img/picture________________B.jpg", '');

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
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'username' => 'test-role',
                'name' => 'Test Role',
                'title' => 'Title Test Role',
                'description' => 'Description Test Role',
                'guide' => 'Just do it!',
                'imageIds' => ['picture________________A.jpg', 'picture________________B.jpg'],
                'fileIds' => ['file___________________1.pdf', 'file___________________2.txt'],
                'parentRole' => 3,
                'indexWithinParent' => 2,
                'featuredIndex' => 6,
                'canHaveChildRoles' => true,
            ],
        ], $result);
    }
}
