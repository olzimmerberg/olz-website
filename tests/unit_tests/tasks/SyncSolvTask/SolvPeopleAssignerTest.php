<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../fake/fake_solv_event.php';
require_once __DIR__.'/../../../fake/fake_solv_result.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/SolvPerson.php';
require_once __DIR__.'/../../../../src/tasks/SyncSolvTask/SolvPeopleAssigner.php';

class FakeSolvPeopleAssignerEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'SolvResult' => new FakeSolvPeopleAssignerSolvResultRepository(),
        ];
    }

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        if ($object instanceof SolvPerson) {
            // Simulate SQL auto-increment.
            if ($object->getId() === null) {
                $object->setId(3);
            }
        }
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }
}

class FakeSolvPeopleAssignerSolvResultRepository {
    public function __construct() {
        $test_runner_result = get_fake_solv_result();
        $test_runner_result->setId(1);
        $this->testRunnerResult = $test_runner_result;

        $typo_result = get_fake_solv_result();
        $typo_result->setId(2);
        $typo_result->setName('Test Runer');
        $this->typoResult = $typo_result;

        $different_result = get_fake_solv_result();
        $different_result->setId(3);
        $different_result->setName('Test Winner');
        $different_result->setBirthYear('92');
        $this->differentResult = $different_result;
    }

    public function getUnassignedSolvResults() {
        return [
            $this->testRunnerResult,
            $this->typoResult,
            $this->differentResult,
        ];
    }

    public function getExactPersonId($solv_result) {
        switch ($solv_result->getId()) {
            case 1:
                // `testRunnerResult` exactly matches an existing, assigned result.
                return 1;
            default:
                // All other unassigned results do not exactly match any existing, assigned result.
                return 0;
        }
    }

    public function getAllAssignedSolvResultPersonData() {
        return [
            [
                'person' => 1,
                'name' => 'Test Runner',
                'birth_year' => '08',
                'domicile' => 'Zürich ZH',
            ],
            [
                'person' => 1,
                'name' => 'Test Runner',
                'birth_year' => '07',
                'domicile' => 'Zürich',
            ],
            [
                'person' => 2,
                'name' => 'Best Runer',
                'birth_year' => '92',
                'domicile' => 'Zürich ZH',
            ],
        ];
    }
}

/**
 * @internal
 * @covers \SolvPeopleAssigner
 */
final class SolvPeopleAssignerTest extends TestCase {
    public function testGetDifferenceBetweenPersonInfo(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();
        $logger = new Logger('SolvPeopleAssignerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvPeopleAssigner($entity_manager);
        $job->setLogger($logger);

        $this->assertSame(0, $job->getDifferenceBetweenPersonInfo(
            'Test', '07', 'Uster',
            'Test', '07', 'Uster',
        ));
        $this->assertSame(1, $job->getDifferenceBetweenPersonInfo(
            'Best', '07', 'Uster',
            'Test', '07', 'Uster',
        ));
        $this->assertSame(3, $job->getDifferenceBetweenPersonInfo(
            'Basti', '07', 'Uster',
            'Test', '07', 'Uster',
        ));
        $this->assertSame(0, $job->getDifferenceBetweenPersonInfo(
            'Test', '07', 'Uster',
            'Test', 7, 'Uster',
        ));
        $this->assertSame(1, $job->getDifferenceBetweenPersonInfo(
            'Test', '07', 'Uster',
            'Test', '08', 'Uster',
        ));
        $this->assertSame(2, $job->getDifferenceBetweenPersonInfo(
            'Test', '07', 'Uster',
            'Test', '07', 'Muster',
        ));
        $this->assertSame(7, $job->getDifferenceBetweenPersonInfo(
            'Test', '07', 'Thalwil',
            'Test', '07', 'Muster',
        ));
        $this->assertSame(2, $job->getDifferenceBetweenPersonInfo(
            'Test', '07', 'Thalwil',
            'Test', '07', '',
        ));
    }

    public function testGetClosestMatchesOfPersonInfo(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();
        $logger = new Logger('SolvPeopleAssignerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvPeopleAssigner($entity_manager);
        $job->setLogger($logger);

        $this->assertSame([
            'difference' => 0,
            'matches' => [
                ['name' => 'Test', 'birth_year' => '07', 'domicile' => 'Uster'],
            ],
        ], $job->getClosestMatchesOfPersonInfo(
            'Test', '07', 'Uster',
            [
                ['name' => 'Test', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '07', 'domicile' => 'Oster'],
            ]
        ));

        $this->assertSame([
            'difference' => 1,
            'matches' => [
                ['name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
                ['name' => 'Text', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '07', 'domicile' => 'Oster'],
            ],
        ], $job->getClosestMatchesOfPersonInfo(
            'Test', '07', 'Uster',
            [
                ['name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
                ['name' => 'Text', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '07', 'domicile' => 'Oster'],
            ]
        ));

        $this->assertSame([
            'difference' => 1,
            'matches' => [
                ['name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
            ],
        ], $job->getClosestMatchesOfPersonInfo(
            'Test', '07', 'Uster',
            [
                ['name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['name' => 'Text', 'birth_year' => '07', 'domicile' => 'Muster'],
                ['name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
                ['name' => 'Best', 'birth_year' => '08', 'domicile' => 'Uster'],
            ]
        ));
    }

    public function testGetUnambiguousPerson(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();
        $logger = new Logger('SolvPeopleAssignerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvPeopleAssigner($entity_manager);
        $job->setLogger($logger);

        $this->assertSame(null, $job->getUnambiguousPerson([
            ['person' => 1],
            ['person' => 2],
            ['person' => 3],
            ['person' => 4],
        ]));
        $this->assertSame(1, $job->getUnambiguousPerson([
            ['person' => 1],
            ['person' => 1],
            ['person' => 1],
            ['person' => 1],
        ]));
        $this->assertSame(4, $job->getUnambiguousPerson([
            ['person' => 4],
        ]));
        $this->assertSame(null, $job->getUnambiguousPerson([]));
    }

    public function testGetMatchingPerson(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();
        $logger = new Logger('SolvPeopleAssignerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvPeopleAssigner($entity_manager);
        $job->setLogger($logger);

        // There is one perfect match.
        $this->assertSame(2, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 1, 'name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['person' => 2, 'name' => 'Test', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['person' => 3, 'name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
                ['person' => 4, 'name' => 'Test', 'birth_year' => '07', 'domicile' => 'Oster'],
            ]
        ));

        // There are multiple matches for the same person.
        $this->assertSame(3, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 3, 'name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['person' => 3, 'name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
            ]
        ));

        // There are matches, but they are for different persons.
        $this->assertSame(null, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 1, 'name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['person' => 2, 'name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
            ]
        ));

        // The matches are too bad.
        $this->assertSame(null, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 1, 'name' => 'Best', 'birth_year' => '08', 'domicile' => 'Oster'],
                ['person' => 1, 'name' => 'Text', 'birth_year' => '07', 'domicile' => 'Muster'],
            ]
        ));
    }

    public function testSolvPeopleAssigner(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();
        $logger = new Logger('SolvPeopleAssignerTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));

        $job = new SolvPeopleAssigner($entity_manager);
        $job->setLogger($logger);
        $job->assignSolvPeople();

        $solv_result_repo = $entity_manager->getRepository('SolvResult');
        $test_runner_result = $solv_result_repo->testRunnerResult;
        $this->assertSame(1, $test_runner_result->getPerson());
        $typo_result = $solv_result_repo->typoResult;
        $this->assertSame(1, $typo_result->getPerson());

        $flushed = $entity_manager->flushed;
        $this->assertSame(1, count($flushed));
        $this->assertSame('Test Winner', $flushed[0]->getName());
        $this->assertSame(null, $flushed[0]->getSameAs());
        $different_result = $solv_result_repo->differentResult;
        $this->assertSame(3, $different_result->getPerson());
    }
}
