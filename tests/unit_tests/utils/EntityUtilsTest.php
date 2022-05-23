<?php

declare(strict_types=1);

require_once __DIR__.'/../../../public/_/model/OlzEntity.php';
require_once __DIR__.'/../../../public/_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../public/_/utils/EntityUtils.php';
require_once __DIR__.'/../../fake/fake_role.php';
require_once __DIR__.'/../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../fake/FakeUsers.php';
require_once __DIR__.'/../common/UnitTestCase.php';

class FakeEntityUtilsRoleRepository {
    public function __construct() {
        $admin_role = get_fake_role();
        $admin_role->setId(2);
        $this->admin_role = $admin_role;
    }

    public function findOneBy($where) {
        if ($where === ['id' => 2]) {
            return $this->admin_role;
        }
        return null;
    }
}

/**
 * @internal
 * @covers \EntityUtils
 */
final class EntityUtilsTest extends UnitTestCase {
    public function testCreateOlzEntity(): void {
        $auth_utils = new FakeAuthUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new FakeEntityManager();
        $role_repo = new FakeEntityUtilsRoleRepository();
        $entity_manager->repositories['Role'] = $role_repo;
        $entity_utils = new EntityUtils();
        $entity_utils->setAuthUtils($auth_utils);
        $entity_utils->setDateUtils($date_utils);
        $entity_utils->setEntityManager($entity_manager);
        $entity = new OlzEntity();

        $entity_utils->createOlzEntity(
            $entity, ['onOff' => 1, 'ownerUserId' => 1, 'ownerRoleId' => 2]);

        $this->assertSame(1, $entity->getOnOff());
        $this->assertSame(FakeUsers::defaultUser(), $entity->getOwnerUser());
        $this->assertSame($role_repo->admin_role, $entity->getOwnerRole());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getCreatedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUsers::adminUser(), $entity->getCreatedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUsers::adminUser(), $entity->getLastModifiedByUser());
    }

    public function testUpdateOlzEntity(): void {
        $auth_utils = new FakeAuthUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $entity_manager = new FakeEntityManager();
        $role_repo = new FakeEntityUtilsRoleRepository();
        $entity_manager->repositories['Role'] = $role_repo;
        $entity_utils = new EntityUtils();
        $entity_utils->setAuthUtils($auth_utils);
        $entity_utils->setDateUtils($date_utils);
        $entity_utils->setEntityManager($entity_manager);
        $then_datetime = new DateTime('2019-01-01 19:30:00');
        $entity = new OlzEntity();
        $entity->setOnOff(1);
        $entity->setOwnerUser(FakeUsers::vorstandUser());
        $entity->setOwnerRole(null);
        $entity->setCreatedAt($then_datetime);
        $entity->setCreatedByUser(FakeUsers::vorstandUser());
        $entity->setLastModifiedAt($then_datetime);
        $entity->setLastModifiedByUser(FakeUsers::vorstandUser());

        $entity_utils->updateOlzEntity(
            $entity, ['onOff' => 1, 'ownerUserId' => 1, 'ownerRoleId' => 2]);

        $user_repo = $entity_manager->repositories['User'];
        $this->assertSame(1, $entity->getOnOff());
        $this->assertSame($user_repo->default_user, $entity->getOwnerUser());
        $this->assertSame($role_repo->admin_role, $entity->getOwnerRole());
        $this->assertSame($then_datetime, $entity->getCreatedAt());
        $this->assertSame(FakeUsers::vorstandUser(), $entity->getCreatedByUser());
        $this->assertSame(
            '2020-03-13 19:30:00',
            $entity->getLastModifiedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(FakeUsers::adminUser(), $entity->getLastModifiedByUser());
    }
}
