<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Roles;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeRoleRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeRole::class;

    public $roleToBeFound;
    public $roleToBeFoundForQuery;
    public $fakeProcessEmailCommandRole;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($this->roleToBeFound !== null) {
            return $this->roleToBeFound;
        }
        if ($this->roleToBeFoundForQuery !== null) {
            $fn = $this->roleToBeFoundForQuery;
            return $fn($criteria);
        }
        if ($criteria === ['username' => 'role'] || $criteria === ['id' => 1]) {
            return FakeRole::defaultRole();
        }
        if ($criteria === ['username' => 'admin_role'] || $criteria === ['id' => 2]) {
            return FakeRole::adminRole();
        }
        if ($criteria === ['username' => 'vorstand_role'] || $criteria === ['id' => 3]) {
            return FakeRole::vorstandRole();
        }
        if (preg_match('/^[3]+$/', strval($criteria['id'] ?? ''))) {
            return FakeRole::subVorstandRole(false, strlen(strval($criteria['id'] ?? '')) - 1);
        }
        if ($criteria === ['username' => 'test'] || $criteria === ['old_username' => 'test']) {
            return null;
        }
        // if ($criteria === ['username' => 'specific']) {
        //     $this->specific_role = FakeRole::defaultRole(true);
        //     $this->specific_role->setPermissions('test');
        //     return $this->specific_role;
        // }
        // if ($criteria === ['username' => 'no']) {
        //     $this->no_access_role = FakeRole::defaultRole(true);
        //     $this->no_access_role->setPermissions('');
        //     return $this->no_access_role;
        // }
        return parent::findOneBy($criteria);
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
            return FakeRole::someRole();
        }
        return null;
    }
}
