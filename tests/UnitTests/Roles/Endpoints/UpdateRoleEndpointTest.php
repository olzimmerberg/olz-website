<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\UpdateRoleEndpoint;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\UpdateRoleEndpoint
 */
final class UpdateRoleEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'id' => FakeOlzRepository::MAXIMAL_ID,
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'username' => 'test',
                'name' => 'Test Role',
                'description' => 'Description Test Role',
                'guide' => 'Just do it!',
                'imageIds' => ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                'fileIds' => ['uploaded_file1.pdf', 'uploaded_file2.txt'],
                'parentRole' => FakeRole::vorstandRole()->getId(),
                'indexWithinParent' => 2,
                'featuredIndex' => 6,
                'canHaveChildRoles' => true,
            ],
        ];
    }

    public function testUpdateRoleEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeRole::maximal(), 'default', 'default', 'role', null, 'roles'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateRoleEndpointNoSuchEntity(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = true;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                ...$this->getValidInput(),
                'id' => FakeOlzRepository::NULL_ID,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 404",
            ], $this->getLogs());

            $this->assertSame(
                [],
                WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls,
            );

            $this->assertSame(404, $err->getCode());
        }
    }

    public function testUpdateRoleEndpointNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());

            $this->assertSame([
                [FakeRole::maximal(), 'default', 'default', 'role', null, 'roles'],
            ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

            $this->assertSame(403, $err->getCode());
        }
    }

    public function testUpdateRoleEndpointParentRoleAccess(): void {
        $id = FakeRole::subVorstandRole(false, 2)->getId();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        WithUtilsCache::get('authUtils')->authenticated_roles = [FakeRole::subVorstandRole(false, 1)];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            ...$this->getValidInput(),
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => $id,
        ], $result);

        $this->assertSame([
            [FakeRole::subVorstandRole(false, 2), null, null, null, null, 'roles'],
            [FakeRole::subVorstandRole(false, 1), null, null, null, null, 'roles'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame($id, $entity->getId());
        $this->assertSame('test', $entity->getUsername());
        $this->assertSame('sub_sub_vorstand_role', $entity->getOldUsername());
        $this->assertSame('Test Role', $entity->getName());
        $this->assertSame('Description Test Role', $entity->getDescription());
        $this->assertSame('Just do it!', $entity->getGuide());
        $this->assertSame(FakeRole::vorstandRole()->getId(), $entity->getParentRoleId());
        $this->assertSame(2, $entity->getIndexWithinParent());
        $this->assertSame(6, $entity->getFeaturedIndex());
        $this->assertTrue($entity->getCanHaveChildRoles());
        $this->assertSame(1, $entity->getOnOff());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

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
        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/roles/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateRoleEndpointRoleAccess(): void {
        $id = FakeRole::subVorstandRole(false, 2)->getId();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        WithUtilsCache::get('authUtils')->authenticated_roles = [FakeRole::subVorstandRole(false, 2)];
        WithUtilsCache::get('entityUtils')->can_update_olz_entity = false;
        $endpoint = new UpdateRoleEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            ...$this->getValidInput(),
            'id' => $id,
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => $id,
        ], $result);

        $this->assertSame([
            [FakeRole::subVorstandRole(false, 2), null, null, null, null, 'roles'],
            [FakeRole::subVorstandRole(false, 1), null, null, null, null, 'roles'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame($id, $entity->getId());
        $this->assertSame('test', $entity->getUsername());
        $this->assertSame('sub_sub_vorstand_role', $entity->getOldUsername());
        $this->assertSame('Test Role', $entity->getName());
        $this->assertSame('Description Test Role', $entity->getDescription());
        $this->assertSame('Just do it!', $entity->getGuide());
        // not updated:
        $this->assertSame(FakeRole::subVorstandRole(false, 1)->getId(), $entity->getParentRoleId());
        // not updated:
        $this->assertSame(0, $entity->getIndexWithinParent());
        // not updated:
        $this->assertNull($entity->getFeaturedIndex());
        // not updated:
        $this->assertTrue($entity->getCanHaveChildRoles());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

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
        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/roles/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }

    public function testUpdateRoleEndpoint(): void {
        $id = FakeOlzRepository::MAXIMAL_ID;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
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

        $result = $endpoint->call($this->getValidInput());

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE OLD:",
            "NOTICE NEW:",
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'id' => $id,
        ], $result);

        $this->assertSame([
            [FakeRole::maximal(), 'default', 'default', 'role', null, 'roles'],
            [FakeRole::vorstandRole(), null, null, null, null, 'roles'],
        ], WithUtilsCache::get('entityUtils')->can_update_olz_entity_calls);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame($id, $entity->getId());
        $this->assertSame('test', $entity->getUsername());
        $this->assertSame('test-role', $entity->getOldUsername());
        $this->assertSame('Test Role', $entity->getName());
        $this->assertSame('Description Test Role', $entity->getDescription());
        $this->assertSame('Just do it!', $entity->getGuide());
        $this->assertSame(FakeRole::vorstandRole()->getId(), $entity->getParentRoleId());
        $this->assertSame(2, $entity->getIndexWithinParent());
        $this->assertSame(6, $entity->getFeaturedIndex());
        $this->assertTrue($entity->getCanHaveChildRoles());
        $this->assertSame(1, $entity->getOnOff());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->update_olz_entity_calls);

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
        $this->assertSame([
            [
                ['uploaded_imageA.jpg', 'uploaded_imageB.jpg'],
                realpath(__DIR__.'/../../../')."/Fake/../UnitTests/tmp/img/roles/{$id}/",
            ],
        ], WithUtilsCache::get('imageUtils')->generatedThumbnails);
    }
}
