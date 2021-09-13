<?php

require_once __DIR__.'/fake_user.php';

class FakeUserRepository {
    public $userToBeFound;

    public function findOneBy($where) {
        if ($this->userToBeFound !== null) {
            return $this->userToBeFound;
        }
        if ($where === ['username' => 'admin'] || $where === ['id' => 2]) {
            $this->admin_user = FakeUsers::adminUser();
            return $this->admin_user;
        }
        if ($where === ['email' => 'vorstand@test.olzimmerberg.ch']) {
            $vorstand_user = get_fake_user();
            $vorstand_user->setId(3);
            $vorstand_user->setUsername('vorstand');
            $vorstand_user->setPasswordHash(password_hash('v0r57and', PASSWORD_DEFAULT));
            $vorstand_user->setZugriff('aktuell ftp');
            $vorstand_user->setRoot('vorstand');
            $this->vorstand_user = $vorstand_user;
            return $vorstand_user;
        }
        if ($where === ['username' => 'noaccess']) {
            $noaccess_user = get_fake_user();
            $noaccess_user->setZugriff('ftp');
            $this->noaccess_user = $noaccess_user;
            return $noaccess_user;
        }
        if ($where === ['username' => 'specific']) {
            $specific_user = get_fake_user();
            $specific_user->setZugriff('test');
            $this->specific_user = $specific_user;
            return $specific_user;
        }
        if ($where === ['username' => 'no']) {
            $no_access_user = get_fake_user();
            $no_access_user->setZugriff('');
            $this->no_access_user = $no_access_user;
            return $no_access_user;
        }
        return null;
    }

    public function findFuzzilyByUsername($username) {
        if ($username === 'someone') {
            $fake_process_email_task_user = get_fake_user();
            $fake_process_email_task_user->setId(1);
            $fake_process_email_task_user->setUsername('someone');
            $fake_process_email_task_user->setFirstName('First');
            $fake_process_email_task_user->setLastName('User');
            $fake_process_email_task_user->setEmail('someone@gmail.com');
            $this->fake_process_email_task_user = $fake_process_email_task_user;
            return $fake_process_email_task_user;
        }
        if ($username === 'no-permission') {
            $user = get_fake_user();
            $user->setUsername('no-permission');
            return $user;
        }
        return null;
    }
}
