<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\UpdateMeasurementsCommand;
use Olz\Common\Utils\DateUtils;
use Olz\Common\Utils\WithUtilsCache;
use Olz\Entity\Measurement;
use Olz\Entity\Members\Member;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Roles\Role;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminReaction;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\FakeMeasurementRepository;
use Olz\Tests\Fake\Entity\FakeSolvPersonRepository;
use Olz\Tests\Fake\Entity\FakeSolvResultRepository;
use Olz\Tests\Fake\Entity\Members\FakeMemberRepository;
use Olz\Tests\Fake\Entity\News\FakeNewsRepository;
use Olz\Tests\Fake\Entity\Roles\FakeRoleRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminReactionRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminRepository;
use Olz\Tests\Fake\Entity\Users\FakeUserRepository;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @coversNothing
 */
class UpdateMeasurementsCommandForTest extends UpdateMeasurementsCommand {
    /** @return array<string, ?string> */
    public function testOnlyGetSektionByWord(): array {
        return $this->getSektionByWord();
    }

    /** @param array<string, ?string> $sektion_by_word */
    public function testOnlyFindSektionForMember(Member $member, array $sektion_by_word): ?string {
        return $this->findSektionForMember($member, $sektion_by_word);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\UpdateMeasurementsCommand
 */
final class UpdateMeasurementsCommandTest extends UnitTestCase {
    public function testUpdateMeasurementsCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        assert($entity_manager instanceof FakeEntityManager);

        $make_user = function (
            int $id,
            string $created_at,
            ?string $joined_on,
            ?string $left_on,
            ?string $deleted_at,
            ?string $password = 'hashed-password',
            ?int $parent_user_id = null,
            ?string $last_login_at = null,
        ): User {
            $user = new User();
            $user->setId($id);
            $user->setCreatedAt(new \DateTime($created_at));
            $user->setJoinedOn($joined_on !== null ? new \DateTime($joined_on) : null);
            $user->setLeftOn($left_on !== null ? new \DateTime($left_on) : null);
            $user->setDeletedAt($deleted_at !== null ? new \DateTime($deleted_at) : null);
            $user->setPasswordHash($password);
            $user->setParentUserId($parent_user_id);
            $user->setLastLoginAt($last_login_at !== null ? new \DateTime($last_login_at) : null);
            return $user;
        };
        $users = [
            // Existing before, active in March and April. Logged in within the last 30 days.
            $make_user(1, '2006-01-01 00:00:00', '2006-01-13', null, null, 'hashed-password', null, '2020-04-10 10:00:00'),
            // Account created in April: only active in April. Logged in within the last 30 days.
            $make_user(2, '2020-04-10 00:00:00', null, null, null, 'hashed-password', null, '2020-04-01 00:00:00'),
            // Left the club on 2020-03-31: member in March, but not in April.
            // Last login before the 30-day window, so not an active user.
            $make_user(3, '2006-01-01 00:00:00', '2006-01-13', '2020-03-31', null, 'hashed-password', null, '2020-03-15 00:00:00'),
            // Deleted before March: not active in March or April.
            $make_user(4, '2006-01-01 00:00:00', '2006-01-13', null, '2020-02-15 00:00:00', 'hashed-password', null, '2020-04-01 00:00:00'),
            // Account created later, but member since 2006: not a member user before 2021-12.
            $make_user(5, '2021-12-01 00:00:00', '2006-01-13', null, null, 'hashed-password', null, '2020-04-01 00:00:00'),
            // Neither a password nor a parent: not a real account, not counted at all.
            $make_user(6, '2006-01-01 00:00:00', '2006-01-13', null, null, null, null, '2020-04-10 00:00:00'),
            // Child account (no password, but a parent): logged in recently, so counted.
            $make_user(7, '2006-01-01 00:00:00', '2006-01-13', null, null, null, 1, '2020-04-12 00:00:00'),
        ];
        $user_repo = $entity_manager->repositories[User::class] ?? null;
        assert($user_repo instanceof FakeUserRepository);
        $user_repo->entitiesToBeFound = $users;

        $make_member = function (int $id, array $data, ?string $deleted_at): Member {
            $member = new Member();
            $member->setId($id);
            $member->setIdent(strval(2000000 + $id));
            $member->setData(json_encode($data) ?: '');
            $member->setUpdates(null);
            $member->setDeletedAt($deleted_at !== null ? new \DateTime($deleted_at) : null);
            return $member;
        };
        $members = [
            // Member in March and April, in the Thalwil Sektion.
            $make_member(1, ['Eintritt' => '13.01.2006', 'Austritt' => '', 'Ort' => 'Thalwil'], null),
            // Left the club on 2020-03-31: member in March, but not in April.
            $make_member(2, ['Eintritt' => '13.01.2006', 'Austritt' => '31.03.2020'], null),
            // Joined later: not a member in March or April.
            $make_member(3, ['Eintritt' => '01.05.2020', 'Austritt' => ''], null),
            // Deleted before March: not a member in March or April.
            $make_member(4, ['Eintritt' => '13.01.2006', 'Austritt' => ''], '2020-02-15 00:00:00'),
            // Member in March and April, in the Thalwil Sektion.
            $make_member(5, ['Eintritt' => '13.01.2006', 'Austritt' => '', 'Ort' => 'Thalwil (ZH)'], null),
        ];
        $member_repo = $entity_manager->repositories[Member::class] ?? null;
        assert($member_repo instanceof FakeMemberRepository);
        $member_repo->entitiesToBeFound = $members;

        // The `sektionen` parent role (id 1 in the fake) with one Sektion child.
        $sektion_role = new Role();
        $sektion_role->setId(501);
        $sektion_role->setUsername('thalwil');
        $sektion_role->setOldUsername(null);
        $sektion_role->setName('Sektion Thalwil, Bezirk Horgen');
        $sektion_role->setDescription('');
        $sektion_role->setParentRoleId(1);
        $sektion_role->setPositionWithinParent(0.0);
        $role_repo = $entity_manager->repositories[Role::class] ?? null;
        assert($role_repo instanceof FakeRoleRepository);
        $role_repo->entitiesToBeFoundForQuery = fn (array $criteria): array => [$sektion_role];

        $make_termin = function (int $id, array $label_idents, string $start_date): Termin {
            $termin = new Termin();
            $termin->setId($id);
            $termin->setStartDate(new \DateTime($start_date));
            foreach ($label_idents as $ident) {
                $label = new TerminLabel();
                $label->setIdent($ident);
                $termin->addLabel($label);
            }
            return $termin;
        };
        $make_reaction = function (int $termin_id, int $user_id, string $emoji): TerminReaction {
            $termin = new Termin();
            $termin->setId($termin_id);
            $user = new User();
            $user->setId($user_id);
            $reaction = new TerminReaction();
            $reaction->setTermin($termin);
            $reaction->setUser($user);
            $reaction->setEmoji($emoji);
            return $reaction;
        };
        $reactions_by_termin = [
            101 => [$make_reaction(101, 1, '👍'), $make_reaction(101, 2, '👍'), $make_reaction(101, 3, '👍')],
            102 => [$make_reaction(102, 1, '👍'), $make_reaction(102, 2, '👍'), $make_reaction(102, 3, '👍'), $make_reaction(102, 4, '👍')],
        ];
        $termin_repo = $entity_manager->repositories[Termin::class] ?? null;
        assert($termin_repo instanceof FakeTerminRepository);
        $termin_repo->entitiesToBeMatched = [
            $make_termin(101, ['training'], '2020-03-05'),
            $make_termin(102, ['training', 'ol'], '2020-03-12'),
            $make_termin(103, ['ol'], '2020-03-19'),
        ];

        $termin_reaction_repo = $entity_manager->repositories[TerminReaction::class] ?? null;
        assert($termin_reaction_repo instanceof FakeTerminReactionRepository);
        $termin_reaction_repo->entitiesToBeFoundForQuery = function (array $criteria) use ($reactions_by_termin): array {
            $termin_id = $criteria['termin'] ?? null;
            if ($termin_id !== null) {
                return $reactions_by_termin[$termin_id] ?? [];
            }
            throw new \Exception("Unexpected reaction criteria: ".json_encode($criteria));
        };

        $news_repo = $entity_manager->repositories[NewsEntry::class] ?? null;
        assert($news_repo instanceof FakeNewsRepository);
        $news_repo->entitiesToBeMatched = [new NewsEntry(), new NewsEntry(), new NewsEntry(), new NewsEntry()];

        $make_solv_person = function (int $id, int $member): SolvPerson {
            $solv_person = new SolvPerson();
            $solv_person->setId($id);
            $solv_person->setMember($member);
            return $solv_person;
        };
        $solv_person_repo = $entity_manager->repositories[SolvPerson::class] ?? null;
        assert($solv_person_repo instanceof FakeSolvPersonRepository);
        $solv_person_repo->entitiesToBeFoundForQuery = fn (array $criteria): array => [
            $make_solv_person(1, 1),
            $make_solv_person(2, 0),
        ];

        $make_solv_result = function (int $event, int $person, int $distance, int $elevation): SolvResult {
            $solv_result = new SolvResult();
            $solv_result->setEvent($event);
            $solv_result->setPerson($person);
            $solv_result->setClassDistance($distance);
            $solv_result->setClassElevation($elevation);
            return $solv_result;
        };
        $solv_results_by_event = [
            20202 => [$make_solv_result(20202, 1, 4500, 200), $make_solv_result(20202, 2, 3000, 100)],
            20201 => [$make_solv_result(20201, 1, 5000, 250)],
        ];
        $solv_result_repo = $entity_manager->repositories[SolvResult::class] ?? null;
        assert($solv_result_repo instanceof FakeSolvResultRepository);
        $solv_result_repo->entitiesToBeFoundForQuery = function (array $criteria) use ($solv_results_by_event): array {
            $event = $criteria['event'] ?? null;
            if ($event !== null) {
                return $solv_results_by_event[$event] ?? [];
            }
            throw new \Exception("Unexpected solv result criteria: ".json_encode($criteria));
        };

        $measurement_repo = $entity_manager->repositories[Measurement::class];
        assert($measurement_repo instanceof FakeMeasurementRepository);
        $measurement_repo->entitiesToBeMatched = [];
        // Stale event metric value of the current month (e.g. from an earlier version).
        $measurement_repo->set('num_news', 2020, 4, 99.0, 0);

        $command = new UpdateMeasurementsCommand();
        $command->setDateUtils(new DateUtils('2020-04-15 12:00:00'));
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame([
            'INFO Running command Olz\Command\UpdateMeasurementsCommand...',
            'INFO Updated measurements for 2020-03',
            'INFO Updated measurements for 2020-04',
            'INFO Successfully ran command Olz\Command\UpdateMeasurementsCommand.',
        ], $this->getLogs());
        $this->assertSame(
            "Running command Olz\\Command\\UpdateMeasurementsCommand...\n".
            "Updated measurements for 2020-03\n".
            "Updated measurements for 2020-04\n".
            "Successfully ran command Olz\\Command\\UpdateMeasurementsCommand.\n",
            $output->fetch(),
        );
        $this->assertSame([
            'num_users|2020|3' => 3.0,
            'num_members|2020|3' => 3.0,
            'num_member_users|2020|3' => 3.0,
            'num_termine|2020|3' => 3.0,
            'num_solv_participations|2020|3' => 2.0,
            'avg_training_participants|2020|3' => 3.5,
            'num_news|2020|3' => 4.0,
            'num_users|2020|4' => 4.0,
            'num_members|2020|4' => 2.0,
            'num_member_users|2020|4' => 2.0,
            'num_termine|2020|4' => 3.0,
            'num_active_users|2020|4' => 3.0,
            'num_members_thalwil|2020|4' => 2.0,
        ], $measurement_repo->measurements);
        // The current month has no event metrics, so the stale row is gone.
        $this->assertSame(['num_news|2020|4'], $measurement_repo->removed);
        // The changes must actually be written.
        $this->assertTrue($entity_manager->flushed);
    }

    public function testGetSektionByWord(): void {
        $this->setSektionRoles([
            $this->makeSektionRole('sektion-thalwil', 'Thalwil', '# Orientierungslauf-Verein Thalwil, Bezirk Horgen'),
            $this->makeSektionRole('sektion-waedenswil', 'Wädenswil', '# Orientierungslauf-Verein Wädenswil'),
        ]);
        $sut = new UpdateMeasurementsCommandForTest();
        $this->assertSame([
            'thalwil' => 'thalwil',
            'wädenswil' => 'waedenswil',
        ], $sut->testOnlyGetSektionByWord());
    }

    public function testFindSektionForMember(): void {
        $sut = new UpdateMeasurementsCommandForTest();
        $this->assertSame('sektion-thalwil', $sut->testOnlyFindSektionForMember($this->makeSektionMember('Thalwil (ZH)'), ['thalwil' => 'sektion-thalwil']));
        $this->assertNull($sut->testOnlyFindSektionForMember($this->makeSektionMember('Horgen'), ['thalwil' => 'sektion-thalwil']));
    }

    /** @param array<Role> $roles */
    private function setSektionRoles(array $roles): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        assert($entity_manager instanceof FakeEntityManager);
        $role_repo = $entity_manager->repositories[Role::class] ?? null;
        assert($role_repo instanceof FakeRoleRepository);
        $role_repo->entitiesToBeFoundForQuery = fn (array $criteria): array => $roles;
    }

    private function makeSektionRole(string $username, string $name, string $description): Role {
        $role = new Role();
        $role->setUsername($username);
        $role->setOldUsername(null);
        $role->setName($name);
        $role->setDescription($description);
        return $role;
    }

    private function makeSektionMember(string $city): Member {
        $member = new Member();
        $member->setId(1);
        $member->setIdent('1000001');
        $member->setData(json_encode(['Ort' => $city]) ?: '');
        return $member;
    }
}
