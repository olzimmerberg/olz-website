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
 * @coversNothing
 */
final class SolvPeopleAssignerTest extends TestCase {
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
