<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

class FakeUserRepository {
    public $userToBeFound;
    public $userToBeFoundForQuery;
    public $fakeProcessEmailCommandUser;

    public function findBy($query) {
        if ($query == ['parent_user' => 2]) {
            return [
                FakeUser::vorstandUser(),
                FakeUser::defaultUser(),
            ];
        }
        $json_query = json_encode($query);
        throw new \Exception("Query no mocked: {$json_query}");
    }

    public function findOneBy($where) {
        if ($this->userToBeFound !== null) {
            return $this->userToBeFound;
        }
        if ($this->userToBeFoundForQuery !== null) {
            $fn = $this->userToBeFoundForQuery;
            return $fn($where);
        }
        if ($where === ['username' => 'user'] || $where === ['id' => 1]) {
            return FakeUser::defaultUser();
        }
        if ($where === ['username' => 'admin'] || $where === ['id' => 2] || $where === ['old_username' => 'admin-old']) {
            return FakeUser::adminUser();
        }
        if (
            $where === ['username' => 'vorstand']
            || $where === ['email' => 'vorstand@staging.olzimmerberg.ch']
            || $where === ['id' => 3]
        ) {
            return FakeUser::vorstandUser();
        }
        if ($where === ['username' => 'parent'] || $where === ['id' => 4]) {
            return FakeUser::parentUser();
        }
        if ($where === ['username' => 'child1'] || $where === ['id' => 5]) {
            return FakeUser::child1User();
        }
        if ($where === ['username' => 'child2'] || $where === ['id' => 6]) {
            return FakeUser::child2User();
        }
        if ($where === ['username' => 'no']) {
            return FakeUser::noAccessUser();
        }
        if ($where === ['username' => 'specific']) {
            return FakeUser::specificAccessUser();
        }
        return null;
    }

    public function findFuzzilyByUsername($username) {
        if ($username === 'someone') {
            $fake_process_email_command_user = FakeUser::defaultUser(true);
            $fake_process_email_command_user->setId(1);
            $fake_process_email_command_user->setUsername('someone');
            $fake_process_email_command_user->setFirstName('First');
            $fake_process_email_command_user->setLastName('User');
            $fake_process_email_command_user->setEmail('someone@gmail.com');
            $this->fakeProcessEmailCommandUser = $fake_process_email_command_user;
            return $fake_process_email_command_user;
        }
        if ($username === 'empty-email') {
            $user = FakeUser::defaultUser(true);
            $user->setId(1);
            $user->setUsername('empty-email');
            $user->setFirstName('Empty');
            $user->setLastName('Email');
            $user->setEmail('');
            return $user;
        }
        if ($username === 'no-permission') {
            $user = FakeUser::defaultUser(true);
            $user->setUsername('no-permission');
            return $user;
        }
        return null;
    }

    public function findFuzzilyByOldUsername($old_username) {
        if ($old_username === 'someone-old') {
            $fake_process_email_command_user = FakeUser::defaultUser(true);
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
            FakeUser::adminUser(),
            FakeUser::vorstandUser(),
        ];
    }
}
