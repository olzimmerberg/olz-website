<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\EntityUtils;
use Olz\Common\Utils\WithUtilsCache;
use Olz\Entity\Common\OlzEntity;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\EntityUtils
 */
final class EntityUtilsTest extends UnitTestCase {
    public function testCreateOlzEntity(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();

        $entity_utils->createOlzEntity(
            $entity,
            ['onOff' => true, 'ownerUserId' => 1, 'ownerRoleId' => 2]
        );

        $this->assertNull($entity->getDeletedAt());
        $this->assertSame(FakeUser::defaultUser(), $entity->getOwnerUser());
        $this->assertSame(FakeRole::adminRole(), $entity->getOwnerRole());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getCreatedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUser::defaultUser(), $entity->getCreatedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUser::defaultUser(), $entity->getLastModifiedByUser());
    }

    public function testCreateOlzEntityDeleted(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();

        $entity_utils->createOlzEntity($entity, ['onOff' => false]);

        $this->assertNotNull($entity->getDeletedAt());
        // A deleted entity must name the user who deleted it.
        $this->assertSame(FakeUser::defaultUser(), $entity->getDeletedByUser());
    }

    public function testDeleteOlzEntity(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();

        $entity_utils->deleteOlzEntity($entity);

        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getDeletedAt()?->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUser::defaultUser(), $entity->getDeletedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUser::defaultUser(), $entity->getLastModifiedByUser());
    }

    public function testRestoreOlzEntity(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $entity_utils = new EntityUtils();
        $then_datetime = new \DateTime('2019-01-01 19:30:00');
        $entity = new OlzEntity();
        $entity->setDeletedAt($then_datetime);
        $entity->setDeletedByUser(FakeUser::vorstandUser());
        $entity->setLastModifiedAt($then_datetime);
        $entity->setLastModifiedByUser(FakeUser::vorstandUser());

        $entity_utils->restoreOlzEntity($entity);

        $this->assertNull($entity->getDeletedAt());
        $this->assertNull($entity->getDeletedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUser::defaultUser(), $entity->getLastModifiedByUser());
    }

    public function testUpdateOlzEntity(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        $entity_utils = new EntityUtils();
        $then_datetime = new \DateTime('2019-01-01 19:30:00');
        $entity = new OlzEntity();
        $entity->setDeletedAt(null);
        $entity->setDeletedByUser(null);
        $entity->setOwnerUser(FakeUser::vorstandUser());
        $entity->setOwnerRole(null);
        $entity->setCreatedAt($then_datetime);
        $entity->setCreatedByUser(FakeUser::vorstandUser());
        $entity->setLastModifiedAt($then_datetime);
        $entity->setLastModifiedByUser(FakeUser::vorstandUser());

        $entity_utils->updateOlzEntity(
            $entity,
            ['onOff' => true, 'ownerUserId' => 1, 'ownerRoleId' => 2]
        );

        $this->assertNull($entity->getDeletedAt());
        $this->assertNull($entity->getDeletedByUser());
        $this->assertSame(FakeUser::defaultUser(), $entity->getOwnerUser());
        $this->assertSame(FakeRole::adminRole(), $entity->getOwnerRole());
        $this->assertSame($then_datetime, $entity->getCreatedAt());
        $this->assertSame(FakeUser::vorstandUser(), $entity->getCreatedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUser::defaultUser(), $entity->getLastModifiedByUser());
    }

    public function testCanUpdateOlzEntityAllPermissions(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => true];
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();

        $this->assertTrue($entity_utils->canUpdateOlzEntity($entity, []));
        $this->assertTrue($entity_utils->canUpdateOlzEntity(null, []));
    }

    public function testCanUpdateOlzEntityEditPermission(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['edit_permission' => true];
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();

        $this->assertTrue($entity_utils->canUpdateOlzEntity(
            $entity,
            [],
            'edit_permission'
        ));
        $this->assertTrue($entity_utils->canUpdateOlzEntity(
            null,
            [],
            'edit_permission'
        ));
    }

    public function testCanUpdateOlzEntityIsOwner(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();
        $entity->setOwnerUser(FakeUser::defaultUser());

        $result = $entity_utils->canUpdateOlzEntity(
            $entity,
            []
        );

        $this->assertTrue($result);
    }

    public function testCanUpdateOlzEntityIsCreatedBy(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();
        $entity->setOwnerUser(null);
        $entity->setCreatedByUser(FakeUser::defaultUser());

        $result = $entity_utils->canUpdateOlzEntity(
            $entity,
            []
        );

        $this->assertTrue($result);
    }

    public function testCanUpdateOlzEntityNoEntityAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();
        $entity->setOwnerUser(null);
        $entity->setCreatedByUser(null);

        $result = $entity_utils->canUpdateOlzEntity(
            $entity,
            []
        );

        $this->assertFalse($result);
    }

    public function testCanUpdateOlzEntityAnonymous(): void {
        WithUtilsCache::get('authUtils')->current_user = null;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['all' => false];
        $entity_utils = new EntityUtils();
        $entity = new OlzEntity();
        $entity->setOwnerUser(FakeUser::defaultUser());
        $entity->setCreatedByUser(FakeUser::defaultUser());

        $result = $entity_utils->canUpdateOlzEntity(
            $entity,
            []
        );

        $this->assertFalse($result);
    }
}
