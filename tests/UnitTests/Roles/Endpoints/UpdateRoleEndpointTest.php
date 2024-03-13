<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Roles\Endpoints\UpdateRoleEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeUpdateRoleEndpointRoleRepository {
    public function findOneBy($where) {
        if ($where === ['id' => 123]) {
            $entry = new Role();
            $entry->setId(123);
            $entry->setUsername('test-role');
            $entry->setOldUsername('old-test-role');
            $entry->setName('Test Role');
            $entry->setTitle('Title Test Role');
            $entry->setDescription('Description Test Role');
            $entry->setGuide('Just do it!');
            $entry->setParentRoleId(8);
            $entry->setIndexWithinParent(2);
            $entry->setFeaturedIndex(6);
            $entry->setCanHaveChildRoles(true);
            $entry->setOnOff(true);
            return $entry;
        }
        if ($where === ['id' => 9999]) {
            return null;
        }
        if ($where === ['username' => 'new-test-role']) {
            return null;
        }
        if ($where === ['old_username' => 'new-test-role']) {
            return null;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\UpdateRoleEndpoint
 */
final class UpdateRoleEndpointTest extends UnitTestCase {
    public const VALID_INPUT = [
        'id' => 123,
        'meta' => [
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ],
        'data' => [
            'username' => 'new-test-role',
            'name' => 'Test Role',
            'title' => 'Title Test Role',
            'description' => 'Description Test Role',
            'guide' => 'Just do it!',
            'imageIds' => ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
            'fileIds' => ['uploaded_file1.pdf', 'uploaded_file2.txt'],
            'parentRole' => 8,
            'indexWithinParent' => 2,
            'featuredIndex' => 6,
            'canHaveChildRoles' => true,
        ],
    ];

    public function testUpdateRoleEndpointIdent(): void {
        $endpoint = new UpdateRoleEndpoint();
        $this->assertSame('UpdateRoleEndpoint', $endpoint->getIdent());
    }

    public function testUpdateRoleEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        $endpoint = new UpdateRoleEndpoint();
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

    public function testUpdateRoleEndpointNoSuchEntity(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeUpdateRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...self::VALID_INPUT,
                'id' => 9999,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());
            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateRoleEndpointNoEntityAccess(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeUpdateRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateRoleEndpoint();
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

    public function testUpdateRoleEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeUpdateRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true, 'all' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageA.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_imageB.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/temp/uploaded_file2.txt', '');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');

        $result = $endpoint->call(self::VALID_INPUT);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => 123,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(123, $entity->getId());
        $this->assertSame('new-test-role', $entity->getUsername());
        $this->assertSame('test-role', $entity->getOldUsername());
        $this->assertSame('Test Role', $entity->getName());
        $this->assertSame('Title Test Role', $entity->getTitle());
        $this->assertSame('Description Test Role', $entity->getDescription());
        $this->assertSame('Just do it!', $entity->getGuide());
        $this->assertSame(8, $entity->getParentRoleId());
        $this->assertSame(2, $entity->getIndexWithinParent());
        $this->assertSame(6, $entity->getFeaturedIndex());
        $this->assertSame(true, $entity->getCanHaveChildRoles());
        $this->assertSame(true, $entity->getOnOff());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

        $id = 123;

        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/img/roles/{$id}/img/",
            ],
            [
                ['uploaded_file1.pdf', 'uploaded_file2.txt'],
                realpath(__DIR__.'/../../../Fake/')."/../UnitTests/tmp/files/roles/{$id}/",
            ],
        ], WithUtilsCache::get('uploadUtils')->move_uploads_calls);
    }
}
