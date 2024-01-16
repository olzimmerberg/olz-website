<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeUserRepository {
    public $userToBeFound;
    public $userToBeFoundForQuery;
    public $fakeProcessEmailCommandUser;

    public $default_user;
    public $admin_user;
    public $vorstand_user;
    public $parent_user;
    public $child1_user;
    public $child2_user;
    public $noaccess_user;
    public $specific_user;
    public $no_access_user;

    public function findOneBy($where) {
        if ($this->userToBeFound !== null) {
            return $this->userToBeFound;
        }
        if ($this->userToBeFoundForQuery !== null) {
            $fn = $this->userToBeFoundForQuery;
            return $fn($where);
        }
        if ($where === ['username' => 'user'] || $where === ['id' => 1]) {
            $this->default_user = FakeUsers::defaultUser();
            return $this->default_user;
        }
        if ($where === ['username' => 'admin'] || $where === ['id' => 2] || $where === ['old_username' => 'admin-old']) {
            $this->admin_user = FakeUsers::adminUser();
            return $this->admin_user;
        }
        if (
            $where === ['username' => 'vorstand']
            || $where === ['email' => 'vorstand@staging.olzimmerberg.ch']
            || $where === ['id' => 3]
        ) {
            $this->vorstand_user = FakeUsers::vorstandUser();
            return $this->vorstand_user;
        }
        if ($where === ['username' => 'parent'] || $where === ['id' => 4]) {
            $this->parent_user = FakeUsers::parentUser();
            return $this->parent_user;
        }
        if ($where === ['username' => 'child1'] || $where === ['id' => 5]) {
            $this->child1_user = FakeUsers::child1User();
            return $this->child1_user;
        }
        if ($where === ['username' => 'child2'] || $where === ['id' => 6]) {
            $this->child2_user = FakeUsers::child2User();
            return $this->child2_user;
        }
        if ($where === ['username' => 'noaccess']) {
            $this->noaccess_user = FakeUsers::defaultUser(true);
            $this->noaccess_user->setPermissions('ftp');
            return $this->noaccess_user;
        }
        if ($where === ['username' => 'specific']) {
            $this->specific_user = FakeUsers::defaultUser(true);
            $this->specific_user->setPermissions('test');
            return $this->specific_user;
        }
        if ($where === ['username' => 'no']) {
            $this->no_access_user = FakeUsers::defaultUser(true);
            $this->no_access_user->setPermissions('');
            return $this->no_access_user;
        }
        return null;
    }

    public function findFuzzilyByUsername($username) {
        if ($username === 'someone') {
            $fake_process_email_command_user = FakeUsers::defaultUser(true);
            $fake_process_email_command_user->setId(1);
            $fake_process_email_command_user->setUsername('someone');
            $fake_process_email_command_user->setFirstName('First');
            $fake_process_email_command_user->setLastName('User');
            $fake_process_email_command_user->setEmail('someone@gmail.com');
            $this->fakeProcessEmailCommandUser = $fake_process_email_command_user;
            return $fake_process_email_command_user;
        }
        if ($username === 'empty-email') {
            $user = FakeUsers::defaultUser(true);
            $user->setId(1);
            $user->setUsername('empty-email');
            $user->setFirstName('Empty');
            $user->setLastName('Email');
            $user->setEmail('');
            return $user;
        }
        if ($username === 'no-permission') {
            $user = FakeUsers::defaultUser(true);
            $user->setUsername('no-permission');
            return $user;
        }
        return null;
    }

    public function findFuzzilyByOldUsername($old_username) {
        if ($old_username === 'someone-old') {
            $fake_process_email_command_user = FakeUsers::defaultUser(true);
            $fake_process_email_command_user->setId(2);
            $fake_process_email_command_user->setUsername('someone-old');
            $fake_process_email_command_user->setFirstName('Old');
            $fake_process_email_command_user->setLastName('User');
            $fake_process_email_command_user->setEmail('someone-old@gmail.com');
            $this->fakeProcessEmailCommandUser = $fake_process_email_command_user;
            return $fake_process_email_command_user;
        }
        return null;
    }

    public function getUsersWithLogin() {
        return [
            FakeUsers::adminUser(),
            FakeUsers::vorstandUser(),
        ];
    }
}
