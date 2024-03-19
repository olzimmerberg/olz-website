<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Roles;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeRoleRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeRole::class;

    public $roleToBeFound;
    public $roleToBeFoundForQuery;
    public $fakeProcessEmailCommandRole;

    public function findOneBy($where) {
        if ($this->roleToBeFound !== null) {
            return $this->roleToBeFound;
        }
        if ($this->roleToBeFoundForQuery !== null) {
            $fn = $this->roleToBeFoundForQuery;
            return $fn($where);
        }
        if ($where === ['username' => 'role'] || $where === ['id' => 1]) {
            return FakeRole::defaultRole();
        }
        if ($where === ['username' => 'admin_role'] || $where === ['id' => 2]) {
            return FakeRole::adminRole();
        }
        if ($where === ['username' => 'vorstand_role'] || $where === ['id' => 3]) {
            return FakeRole::vorstandRole();
        }
        if (preg_match('/^[3]+$/', strval($where['id'] ?? ''))) {
            return FakeRole::subVorstandRole(false, strlen(strval($where['id'] ?? '')) - 1);
        }
        if ($where === ['username' => 'test'] || $where === ['old_username' => 'test']) {
            return null;
        }
        // if ($where === ['username' => 'specific']) {
        //     $this->specific_role = FakeRole::defaultRole(true);
        //     $this->specific_role->setPermissions('test');
        //     return $this->specific_role;
        // }
        // if ($where === ['username' => 'no']) {
        //     $this->no_access_role = FakeRole::defaultRole(true);
        //     $this->no_access_role->setPermissions('');
        //     return $this->no_access_role;
        // }
        return parent::findOneBy($where);
    }

    public function findFuzzilyByUsername($username) {
        if ($username === 'somerole') {
            return FakeRole::someRole();
        }
        if ($username === 'no-role-permission') {
            $role = FakeRole::defaultRole(true);
            $role->setUsername('no-role-permission');
            return $role;
        }
        return null;
    }

    public function findFuzzilyByOldUsername($old_username) {
        if ($old_username === 'somerole-old') {
            return FakeRole::someOldRole();
        }
        return null;
    }
}
