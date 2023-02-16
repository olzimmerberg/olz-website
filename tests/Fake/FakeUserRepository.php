<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeUserRepository {
    public $userToBeFound;
    public $userToBeFoundForQuery;
    public $fakeProcessEmailTaskUser;

    public $default_user;
    public $admin_user;
    public $vorstand_user;
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
        if ($where === ['email' => 'vorstand@test.olzimmerberg.ch'] || $where === ['id' => 3]) {
            $this->vorstand_user = FakeUsers::vorstandUser();
            return $this->vorstand_user;
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
            $fake_process_email_task_user = FakeUsers::defaultUser(true);
            $fake_process_email_task_user->setId(1);
            $fake_process_email_task_user->setUsername('someone');
            $fake_process_email_task_user->setFirstName('First');
            $fake_process_email_task_user->setLastName('User');
            $fake_process_email_task_user->setEmail('someone@gmail.com');
            $this->fakeProcessEmailTaskUser = $fake_process_email_task_user;
            return $fake_process_email_task_user;
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
            $fake_process_email_task_user = FakeUsers::defaultUser(true);
            $fake_process_email_task_user->setId(2);
            $fake_process_email_task_user->setUsername('someone-old');
            $fake_process_email_task_user->setFirstName('Old');
            $fake_process_email_task_user->setLastName('User');
            $fake_process_email_task_user->setEmail('someone-old@gmail.com');
            $this->fakeProcessEmailTaskUser = $fake_process_email_task_user;
            return $fake_process_email_task_user;
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
