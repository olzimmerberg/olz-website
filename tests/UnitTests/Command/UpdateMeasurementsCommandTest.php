<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\UpdateMeasurementsCommand;
use Olz\Common\Utils\DateUtils;
use Olz\Common\Utils\WithUtilsCache;
use Olz\Entity\Measurement;
use Olz\Entity\Members\Member;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminReaction;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\FakeMeasurementRepository;
use Olz\Tests\Fake\Entity\FakeSolvPersonRepository;
use Olz\Tests\Fake\Entity\FakeSolvResultRepository;
use Olz\Tests\Fake\Entity\Members\FakeMemberRepository;
use Olz\Tests\Fake\Entity\News\FakeNewsRepository;
use Olz\Tests\Fake\Entity\Startseite\FakeWeeklyPictureRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminReactionRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminRepository;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\Entity\Users\FakeUserRepository;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \Olz\Command\UpdateMeasurementsCommand
 */
final class UpdateMeasurementsCommandTest extends UnitTestCase {
    public function testUpdateMeasurementsCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        assert($entity_manager instanceof FakeEntityManager);

        $user_repo = $entity_manager->repositories[User::class] ?? null;
        assert($user_repo instanceof FakeUserRepository);
        $user_repo->entitiesToBeFoundForQuery = function (array $criteria): array {
            if ($criteria === ['on_off' => 1]) {
                return array_fill(0, 10, FakeUser::minimal());
            }
            throw new \Exception("Unexpected user criteria: ".json_encode($criteria));
        };

        $active_user = new User();
        $active_user->setOnOff(1);
        $members = [];
        for ($i = 0; $i < 8; $i++) {
            $member = new Member();
            $member->setId($i + 1);
            $member->setOnOff(1);
            $member->setIdent(strval(1000000 + $i));
            $member->setData('');
            $member->setUpdates(null);
            $member->setUser($i < 7 ? $active_user : null);
            $members[] = $member;
        }
        $member_repo = $entity_manager->repositories[Member::class] ?? null;
        assert($member_repo instanceof FakeMemberRepository);
        $member_repo->entitiesToBeFoundForQuery = function (array $criteria) use ($members): array {
            if ($criteria === ['on_off' => 1]) {
                return $members;
            }
            throw new \Exception("Unexpected member criteria: ".json_encode($criteria));
        };

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

        $weekly_picture_repo = $entity_manager->repositories[WeeklyPicture::class] ?? null;
        assert($weekly_picture_repo instanceof FakeWeeklyPictureRepository);
        $weekly_picture_repo->entitiesToBeMatched = [new WeeklyPicture(), new WeeklyPicture()];

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

        $command = new UpdateMeasurementsCommand();
        $command->setDateUtils(new DateUtils('2020-04-15 12:00:00'));
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
        $logs = array_values(array_filter(
            $this->getLogs(),
            fn (string $log): bool => !str_starts_with($log, 'INFO DB:'),
        ));
        $this->assertSame([
            'INFO Running command Olz\Command\UpdateMeasurementsCommand...',
            'INFO Users & Members measurements updated:',
            'INFO   num_users: 10',
            'INFO   num_members: 8',
            'INFO   num_member_users: 7',
            'INFO Termine & Anlässe measurements updated:',
            'INFO   num_termine: 3',
            'INFO   num_solv_participations: 2',
            'INFO   avg_training_participants: 3.5',
            'INFO Website-Aktivität measurements updated:',
            'INFO   num_news: 4',
            'INFO Measurements updated.',
            'INFO Successfully ran command Olz\Command\UpdateMeasurementsCommand.',
        ], $logs);
        $this->assertSame(
            "Running command Olz\\Command\\UpdateMeasurementsCommand...\n".
            "Users & Members measurements updated:\n".
            "  num_users: 10\n".
            "  num_members: 8\n".
            "  num_member_users: 7\n".
            "Termine & Anlässe measurements updated:\n".
            "  num_termine: 3\n".
            "  num_solv_participations: 2\n".
            "  avg_training_participants: 3.5\n".
            "Website-Aktivität measurements updated:\n".
            "  num_news: 4\n".
            "Measurements updated.\n".
            "Successfully ran command Olz\\Command\\UpdateMeasurementsCommand.\n",
            $output->fetch(),
        );

        $measurement_repo = $entity_manager->repositories[Measurement::class];
        assert($measurement_repo instanceof FakeMeasurementRepository);
        $this->assertSame([
            'num_users|2020|4' => 10.0,
            'num_members|2020|4' => 8.0,
            'num_member_users|2020|4' => 7.0,
            'num_termine|2020|4' => 3.0,
            'num_solv_participations|2020|3' => 2.0,
            'avg_training_participants|2020|3' => 3.5,
            'num_news|2020|3' => 4.0,
        ], $measurement_repo->measurements);
    }
}
