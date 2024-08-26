<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\User;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<User>
 */
class FakeUserRepository extends FakeOlzRepository {
    public string $olzEntityClass = User::class;

    public ?User $userToBeFound = null;
    public mixed $userToBeFoundForQuery = null;
    public ?User $fakeProcessEmailCommandUser = null;

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        if ($criteria == ['parent_user' => 2]) {
            return [
                FakeUser::vorstandUser(),
                FakeUser::defaultUser(),
            ];
        }
        $json_criteria = json_encode($criteria);
        throw new \Exception("criteria no mocked: {$json_criteria}");
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($this->userToBeFound !== null) {
            return $this->userToBeFound;
        }
        if ($this->userToBeFoundForQuery !== null) {
            $fn = $this->userToBeFoundForQuery;
            return $fn($criteria);
        }
        if ($criteria === ['username' => 'user'] || $criteria === ['id' => 1]) {
            return FakeUser::defaultUser();
        }
        if (
            $criteria === ['username' => 'admin']
            || $criteria === ['email' => 'admin@gmail.com']
            || $criteria === ['id' => 2]
            || $criteria === ['old_username' => 'admin-old']
        ) {
            return FakeUser::adminUser();
        }
        if (
            $criteria === ['username' => 'vorstand']
            || $criteria === ['email' => 'vorstand@staging.olzimmerberg.ch']
            || $criteria === ['id' => 3]
        ) {
            return FakeUser::vorstandUser();
        }
        if ($criteria === ['username' => 'parent'] || $criteria === ['id' => 4]) {
            return FakeUser::parentUser();
        }
        if (
            $criteria === ['username' => 'child1']
            || $criteria === ['email' => 'child1@gmail.com']
            || $criteria === ['id' => 5]
        ) {
            return FakeUser::child1User();
        }
        if ($criteria === ['username' => 'child2'] || $criteria === ['id' => 6]) {
            return FakeUser::child2User();
        }
        if ($criteria === ['username' => 'no']) {
            return FakeUser::noAccessUser();
        }
        if ($criteria === ['username' => 'specific']) {
            return FakeUser::specificAccessUser();
        }
        return null;
    }

    public function findUserFuzzilyByUsername(string $username): ?User {
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

    public function findUserFuzzilyByOldUsername(string $old_username): ?User {
        if ($old_username === 'someone-old') {
            $fake_process_email_command_user = FakeUser::defaultUser(true);
            $fake_process_email_command_user->setId(2);
            $fake_process_email_command_user->setUsername('someone');
            $fake_process_email_command_user->setOldUsername('someone-old');
            $fake_process_email_command_user->setFirstName('Old');
            $fake_process_email_command_user->setLastName('User');
            $fake_process_email_command_user->setEmail('someone-old@gmail.com');
            $this->fakeProcessEmailCommandUser = $fake_process_email_command_user;
            return $fake_process_email_command_user;
        }
        return null;
    }

    /** @return array<User> */
    public function getUsersWithLogin(): array {
        return [
            FakeUser::adminUser(),
            FakeUser::vorstandUser(),
        ];
    }
}
