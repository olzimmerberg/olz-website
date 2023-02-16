<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeRoleRepository {
    public $roleToBeFound;
    public $roleToBeFoundForQuery;
    public $fakeProcessEmailTaskRole;

    public $default_role;
    public $admin_role;
    public $vorstand_role;

    public function findOneBy($where) {
        if ($this->roleToBeFound !== null) {
            return $this->roleToBeFound;
        }
        if ($this->roleToBeFoundForQuery !== null) {
            $fn = $this->roleToBeFoundForQuery;
            return $fn($where);
        }
        if ($where === ['username' => 'role'] || $where === ['id' => 1]) {
            $this->default_role = FakeRoles::defaultRole();
            return $this->default_role;
        }
        if ($where === ['username' => 'admin_role'] || $where === ['id' => 2]) {
            $this->admin_role = FakeRoles::adminRole();
            return $this->admin_role;
        }
        if ($where === ['username' => 'vorstand_role'] || $where === ['id' => 3]) {
            $this->vorstand_role = FakeRoles::vorstandRole();
            return $this->vorstand_role;
        }
        // if ($where === ['username' => 'noaccess']) {
        //     $this->noaccess_role = FakeRoles::defaultRole(true);
        //     $this->noaccess_role->setPermissions('ftp');
        //     return $this->noaccess_role;
        // }
        // if ($where === ['username' => 'specific']) {
        //     $this->specific_role = FakeRoles::defaultRole(true);
        //     $this->specific_role->setPermissions('test');
        //     return $this->specific_role;
        // }
        // if ($where === ['username' => 'no']) {
        //     $this->no_access_role = FakeRoles::defaultRole(true);
        //     $this->no_access_role->setPermissions('');
        //     return $this->no_access_role;
        // }
        return null;
    }

    public function findFuzzilyByUsername($username) {
        if ($username === 'somerole') {
            $fake_process_email_task_role = FakeRoles::defaultRole(true);
            $fake_process_email_task_role->setId(1);
            $fake_process_email_task_role->setUsername('somerole');
            $fake_process_email_task_role->addUser(FakeUsers::adminUser());
            $fake_process_email_task_role->addUser(FakeUsers::vorstandUser());
            $this->fakeProcessEmailTaskRole = $fake_process_email_task_role;
            return $fake_process_email_task_role;
        }
        if ($username === 'no-role-permission') {
            $role = FakeRoles::defaultRole(true);
            $role->setUsername('no-role-permission');
            return $role;
        }
        return null;
    }

    public function findFuzzilyByOldUsername($old_username) {
        if ($old_username === 'somerole-old') {
            $fake_process_email_task_role = FakeRoles::defaultRole(true);
            $fake_process_email_task_role->setId(2);
            $fake_process_email_task_role->setUsername('somerole-old');
            $fake_process_email_task_role->addUser(FakeUsers::adminUser());
            $fake_process_email_task_role->addUser(FakeUsers::vorstandUser());
            $this->fakeProcessEmailTaskRole = $fake_process_email_task_role;
            return $fake_process_email_task_role;
        }
        return null;
    }
}
