<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Roles\Endpoints;

use Olz\Roles\Endpoints\CreateRoleEndpoint;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Roles\Endpoints\CreateRoleEndpoint
 */
final class CreateRoleEndpointTest extends UnitTestCase {
    /** @return array<string, mixed> */
    protected function getValidInput(): array {
        return [
            'meta' => [
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ],
            'data' => [
                'username' => 'test',
                'name' => 'Test Role',
                'title' => 'Title Test Role',
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

    public function testCreateRoleEndpointIdent(): void {
        $endpoint = new CreateRoleEndpoint();
        $this->assertSame('CreateRoleEndpoint', $endpoint->getIdent());
    }

    public function testCreateRoleEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => false];
        $endpoint = new CreateRoleEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call($this->getValidInput());
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testCreateRoleEndpoint(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['roles' => true];
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $endpoint = new CreateRoleEndpoint();
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
            "INFO Valid user response",
        ], $this->getLogs());

        $this->assertSame([
            'status' => 'OK',
            'id' => FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $entity = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $entity->getId());
        $this->assertSame('test', $entity->getUsername());
        $this->assertNull($entity->getOldUsername());
        $this->assertSame('Test Role', $entity->getName());
        $this->assertSame('Title Test Role', $entity->getTitle());
        $this->assertSame('Description Test Role', $entity->getDescription());
        $this->assertSame('Just do it!', $entity->getGuide());
        $this->assertSame('', $entity->getPage());
        $this->assertSame('', $entity->getPermissions());
        $this->assertSame(FakeRole::vorstandRole()->getId(), $entity->getParentRoleId());
        $this->assertSame(2, $entity->getIndexWithinParent());
        $this->assertSame(6, $entity->getFeaturedIndex());
        $this->assertTrue($entity->getCanHaveChildRoles());

        $this->assertSame([
            [$entity, 1, 1, 1],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);

        $id = FakeEntityManager::AUTO_INCREMENT_ID;

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
