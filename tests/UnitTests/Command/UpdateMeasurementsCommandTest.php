<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\UpdateMeasurementsCommand;
use Olz\Entity\Measurement;
use Olz\Entity\Members\Member;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\FakeMeasurementRepository;
use Olz\Tests\Fake\Entity\Members\FakeMemberRepository;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\Fake\Entity\Users\FakeUserRepository;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
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

        $command = new UpdateMeasurementsCommand();
        $command->setDateUtils(new DateUtils('2020-03-13 12:00:00'));
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame([
            'INFO Running command Olz\Command\UpdateMeasurementsCommand...',
            'INFO Users & Members measurements updated:',
            'INFO   num_users: 10',
            'INFO   num_members: 8',
            'INFO   num_member_users: 7',
            'INFO Measurements updated.',
            'INFO Successfully ran command Olz\Command\UpdateMeasurementsCommand.',
        ], $this->getLogs());
        $this->assertSame(
            "Running command Olz\\Command\\UpdateMeasurementsCommand...\n".
            "Users & Members measurements updated:\n".
            "  num_users: 10\n".
            "  num_members: 8\n".
            "  num_member_users: 7\n".
            "Measurements updated.\n".
            "Successfully ran command Olz\\Command\\UpdateMeasurementsCommand.\n",
            $output->fetch(),
        );

        $measurement_repo = $entity_manager->repositories[Measurement::class];
        assert($measurement_repo instanceof FakeMeasurementRepository);
        $this->assertSame([
            'num_users|2020|3' => 10.0,
            'num_members|2020|3' => 8.0,
            'num_member_users|2020|3' => 7.0,
        ], $measurement_repo->measurements);
    }
}
