<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Roles\Endpoints\EditRoleEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeEditRoleEndpointRoleRepository {
    public function findOneBy($where) {
        // Minimal
        if ($where === ['id' => 12]) {
            $entry = new Role();
            $entry->setId(12);
            $entry->setName('');
            $entry->setOnOff(true);
            return $entry;
        }
        // Empty
        if ($where === ['id' => 123]) {
            $entry = new Role();
            $entry->setId(123);
            $entry->setUsername('');
            $entry->setOldUsername('');
            $entry->setName('');
            $entry->setTitle('');
            $entry->setDescription('');
            $entry->setGuide('');
            $entry->setParentRoleId(null);
            $entry->setIndexWithinParent(-1);
            $entry->setFeaturedIndex(null);
            $entry->setCanHaveChildRoles(false);
            $entry->setOnOff(false);
            return $entry;
        }
        // Maximal
        if ($where === ['id' => 1234]) {
            $entry = new Role();
            $entry->setId(1234);
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
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\EditRoleEndpoint
 */
final class EditRoleEndpointTest extends UnitTestCase {
    public function testEditRoleEndpointIdent(): void {
        $endpoint = new EditRoleEndpoint();
        $this->assertSame('EditRoleEndpoint', $endpoint->getIdent());
    }

    public function testEditRoleEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        $endpoint = new EditRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
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

    public function testEditRoleEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeEditRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        $endpoint = new EditRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
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

    public function testEditRoleEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeEditRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new EditRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'id' => 123,
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

    public function testEditRoleEndpointMinimal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeEditRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__.'/../../tmp/files/roles/12/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');
        mkdir(__DIR__.'/../../tmp/img/roles/12/');
        mkdir(__DIR__.'/../../tmp/img/roles/12/img/');

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

    public function testEditRoleEndpointEmpty(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeEditRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__.'/../../tmp/files/roles/123/');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');
        mkdir(__DIR__.'/../../tmp/img/roles/123/');
        mkdir(__DIR__.'/../../tmp/img/roles/123/img/');

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

    public function testEditRoleEndpointMaximal(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        $entity_manager = WithUtilsCache::get('entityManager');
        $repo = new FakeEditRoleEndpointRoleRepository();
        $entity_manager->repositories[Role::class] = $repo;
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new EditRoleEndpoint();
        $endpoint->runtimeSetup();

        mkdir(__DIR__.'/../../tmp/temp/');
        mkdir(__DIR__.'/../../tmp/files/');
        mkdir(__DIR__.'/../../tmp/files/roles/');
        mkdir(__DIR__.'/../../tmp/files/roles/1234/');
        file_put_contents(__DIR__.'/../../tmp/files/roles/1234/file___________________1.pdf', '');
        file_put_contents(__DIR__.'/../../tmp/files/roles/1234/file___________________2.txt', '');
        mkdir(__DIR__.'/../../tmp/img/');
        mkdir(__DIR__.'/../../tmp/img/roles/');
        mkdir(__DIR__.'/../../tmp/img/roles/1234/');
        mkdir(__DIR__.'/../../tmp/img/roles/1234/img');
        file_put_contents(__DIR__.'/../../tmp/img/roles/1234/img/picture________________A.jpg', '');
        file_put_contents(__DIR__.'/../../tmp/img/roles/1234/img/picture________________B.jpg', '');

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
                'ownerUserId' => null,
                'ownerRoleId' => null,
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
                'parentRole' => 8,
                'indexWithinParent' => 2,
                'featuredIndex' => 6,
                'canHaveChildRoles' => true,
            ],
        ], $result);
    }
}
