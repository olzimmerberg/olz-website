<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\SyncSolvMergePeopleCommand;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeSolvPerson;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @extends FakeOlzRepository<SolvPerson>
 */
class FakeSyncSolvMergePeopleCommandSolvPersonRepository extends FakeOlzRepository {
    public SolvPerson $targetPerson;
    public SolvPerson $samePerson;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $target_person = FakeSolvPerson::defaultSolvPerson(true);
        $target_person->setId(1);
        $this->targetPerson = $target_person;

        $same_person = FakeSolvPerson::defaultSolvPerson(true);
        $same_person->setId(2);
        $same_person->setSameAs(1);
        $same_person->setName('Test Runer');
        $this->samePerson = $same_person;
    }

    /** @return array<array{id: int, same_as: int}> */
    public function getSolvPersonsMarkedForMerge(): array {
        return [
            ['id' => 2, 'same_as' => 1],
        ];
    }
}

/**
 * @extends FakeOlzRepository<SolvResult>
 */
class FakeSyncSolvMergePeopleCommandSolvResultRepository extends FakeOlzRepository {
    /** @var array<array{old: int, new: int}> */
    public array $merged = [];

    public function solvPersonHasResults(int $person_id): bool {
        switch ($person_id) {
            case 1:
                return true;
            case 2:
                return true;
            default:
                throw new \Exception("This should never be called");
        }
    }

    public function mergePerson(int $old_person_id, int $new_person_id): bool {
        $this->merged[] = ['old' => $old_person_id, 'new' => $new_person_id];
        return true;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvMergePeopleCommand
 */
final class SyncSolvMergePeopleCommandTest extends UnitTestCase {
    public function testSyncSolvMergePeopleCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $solv_person_repo = new FakeSyncSolvMergePeopleCommandSolvPersonRepository($entity_manager);
        $entity_manager->repositories[SolvPerson::class] = $solv_person_repo;
        $solv_result_repo = new FakeSyncSolvMergePeopleCommandSolvResultRepository($entity_manager);
        $entity_manager->repositories[SolvResult::class] = $solv_result_repo;

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SyncSolvMergePeopleCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncSolvMergePeopleCommand...',
            'INFO Merge person 2 into 1.',
            'WARNING There are still results assigned to person 2.',
            'INFO Successfully ran command Olz\Command\SyncSolvMergePeopleCommand.',
        ], $this->getLogs());

        $flushed = $entity_manager->flushed_persisted;
        $this->assertCount(0, $flushed);

        $solv_result_repo = $entity_manager->getRepository(SolvResult::class);
        $this->assertSame([['old' => 2, 'new' => 1]], $solv_result_repo->merged);
    }
}
