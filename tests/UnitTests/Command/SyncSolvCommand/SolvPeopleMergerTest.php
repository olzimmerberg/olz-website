<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SyncSolvCommand;

use Olz\Command\SyncSolvCommand\SolvPeopleMerger;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class FakeSolvPeopleMergerSolvPersonRepository {
    public $targetPerson = [];
    public $samePerson = [];

    public function __construct() {
        $target_person = Fake\FakeSolvPerson::defaultSolvPerson(true);
        $target_person->setId(1);
        $this->targetPerson = $target_person;

        $same_person = Fake\FakeSolvPerson::defaultSolvPerson(true);
        $same_person->setId(2);
        $same_person->setSameAs(1);
        $same_person->setName('Test Runer');
        $this->samePerson = $same_person;
    }

    public function getSolvPersonsMarkedForMerge() {
        return [
            ['id' => 2, 'same_as' => 1],
        ];
    }
}

class FakeSolvPeopleMergerSolvResultRepository {
    public $merged = [];

    public function solvPersonHasResults($person_id) {
        switch ($person_id) {
            case 1:
                return true;
            case 2:
                return true;
            default:
                throw new \Exception("This should never be called");
        }
    }

    public function mergePerson($old_person_id, $new_person_id) {
        $this->merged[] = ['old' => $old_person_id, 'new' => $new_person_id];
        return true;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvCommand\SolvPeopleMerger
 */
final class SolvPeopleMergerTest extends UnitTestCase {
    public function testSolvPeopleMerger(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $solv_person_repo = new FakeSolvPeopleMergerSolvPersonRepository();
        $entity_manager->repositories[SolvPerson::class] = $solv_person_repo;
        $solv_result_repo = new FakeSolvPeopleMergerSolvResultRepository();
        $entity_manager->repositories[SolvResult::class] = $solv_result_repo;

        $job = new SolvPeopleMerger();
        $job->setEntityManager($entity_manager);

        $job->mergeSolvPeople();

        $flushed = $entity_manager->flushed_persisted;
        $this->assertSame(0, count($flushed));

        $solv_result_repo = $entity_manager->getRepository(SolvResult::class);
        $this->assertSame([['old' => 2, 'new' => 1]], $solv_result_repo->merged);
    }
}
