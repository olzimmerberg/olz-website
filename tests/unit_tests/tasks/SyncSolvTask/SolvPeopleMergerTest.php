<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../fake/fake_solv_person.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../_/tasks/SyncSolvTask/SolvPeopleMerger.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeSolvPeopleMergerSolvPersonRepository {
    public function __construct() {
        $target_person = get_fake_solv_person();
        $target_person->setId(1);
        $this->targetPerson = $target_person;

        $same_person = get_fake_solv_person();
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
 * @covers \SolvPeopleMerger
 */
final class SolvPeopleMergerTest extends UnitTestCase {
    public function testSolvPeopleMerger(): void {
        $entity_manager = new FakeEntityManager();
        $solv_person_repo = new FakeSolvPeopleMergerSolvPersonRepository();
        $entity_manager->repositories['SolvPerson'] = $solv_person_repo;
        $solv_result_repo = new FakeSolvPeopleMergerSolvResultRepository();
        $entity_manager->repositories['SolvResult'] = $solv_result_repo;
        $logger = new Logger('SolvPeopleMergerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvPeopleMerger($entity_manager);
        $job->setLogger($logger);
        $job->mergeSolvPeople();

        $flushed = $entity_manager->flushed_persisted;
        $this->assertSame(0, count($flushed));

        $solv_result_repo = $entity_manager->getRepository('SolvResult');
        $this->assertSame([['old' => 2, 'new' => 1]], $solv_result_repo->merged);
    }
}
