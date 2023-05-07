<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SyncSolvCommand;

use Olz\Command\SyncSolvCommand\SolvPeopleAssigner;
use Olz\Entity\SolvResult;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeSolvPeopleAssignerEntityManager extends Fake\FakeEntityManager {
    public function __construct() {
        $this->repositories = [
            SolvResult::class => new FakeSolvPeopleAssignerSolvResultRepository(),
        ];
    }
}

class FakeSolvPeopleAssignerSolvResultRepository {
    public $testRunnerResult;
    public $typoResult;
    public $differentResult;

    public function __construct() {
        $test_runner_result = Fake\FakeSolvResult::defaultSolvResult(true);
        $test_runner_result->setId(1);
        $this->testRunnerResult = $test_runner_result;

        $typo_result = Fake\FakeSolvResult::defaultSolvResult(true);
        $typo_result->setId(2);
        $typo_result->setName('Test Runer');
        $this->typoResult = $typo_result;

        $different_result = Fake\FakeSolvResult::defaultSolvResult(true);
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
 *
 * @covers \Olz\Command\SyncSolvCommand\SolvPeopleAssigner
 */
final class SolvPeopleAssignerTest extends UnitTestCase {
    public function testGetDifferenceBetweenPersonInfo(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();

        $job = new SolvPeopleAssigner();
        $job->setEntityManager($entity_manager);

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

        $this->assertSame([
        ], $this->getLogs());
    }

    public function testGetClosestMatchesOfPersonInfo(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();

        $job = new SolvPeopleAssigner();
        $job->setEntityManager($entity_manager);

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

        $this->assertSame([
        ], $this->getLogs());
    }

    public function testGetUnambiguousPerson(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();

        $job = new SolvPeopleAssigner();
        $job->setEntityManager($entity_manager);

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

        $this->assertSame([
        ], $this->getLogs());
    }

    public function testGetMatchingPerson(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();

        $job = new SolvPeopleAssigner();
        $job->setEntityManager($entity_manager);

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
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
            INFO Closest matches (difference 0): [
                {
                    "person": 2,
                    "name": "Test",
                    "birth_year": "07",
                    "domicile": "Uster"
                }
            ]
            ZZZZZZZZZZ,
            'INFO  => Matching person found: 2.',
        ], $this->getLogs());
        WithUtilsCache::get('log')->handler->resetRecords();

        // There are multiple matches for the same person.
        $this->assertSame(3, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 3, 'name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['person' => 3, 'name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
            ]
        ));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
            INFO Closest matches (difference 1): [
                {
                    "person": 3,
                    "name": "Best",
                    "birth_year": "07",
                    "domicile": "Uster"
                },
                {
                    "person": 3,
                    "name": "Test",
                    "birth_year": "08",
                    "domicile": "Uster"
                }
            ]
            ZZZZZZZZZZ,
            'INFO  => Matching person found: 3.',
        ], $this->getLogs());
        WithUtilsCache::get('log')->handler->resetRecords();

        // There are matches, but they are for different persons.
        $this->assertSame(null, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 1, 'name' => 'Best', 'birth_year' => '07', 'domicile' => 'Uster'],
                ['person' => 2, 'name' => 'Test', 'birth_year' => '08', 'domicile' => 'Uster'],
            ]
        ));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
            INFO Closest matches (difference 1): [
                {
                    "person": 1,
                    "name": "Best",
                    "birth_year": "07",
                    "domicile": "Uster"
                },
                {
                    "person": 2,
                    "name": "Test",
                    "birth_year": "08",
                    "domicile": "Uster"
                }
            ]
            ZZZZZZZZZZ,
            'INFO  => No matching person found (closest matches contain different persons).',
        ], $this->getLogs());
        WithUtilsCache::get('log')->handler->resetRecords();

        // The matches are too bad.
        $this->assertSame(null, $job->getMatchingPerson(
            'Test', '07', 'Uster',
            [
                ['person' => 1, 'name' => 'Best', 'birth_year' => '08', 'domicile' => 'Oster'],
                ['person' => 1, 'name' => 'Text', 'birth_year' => '07', 'domicile' => 'Muster'],
            ]
        ));
        $this->assertSame([
            <<<'ZZZZZZZZZZ'
            INFO Closest matches (difference 3): [
                {
                    "person": 1,
                    "name": "Best",
                    "birth_year": "08",
                    "domicile": "Oster"
                },
                {
                    "person": 1,
                    "name": "Text",
                    "birth_year": "07",
                    "domicile": "Muster"
                }
            ]
            ZZZZZZZZZZ,
            'INFO  => No matching person found (difference too high).',
            'NOTICE Unclear case. Maybe update logic?',
        ], $this->getLogs());
        WithUtilsCache::get('log')->handler->resetRecords();
    }

    public function testSolvPeopleAssigner(): void {
        $entity_manager = new FakeSolvPeopleAssignerEntityManager();

        $job = new SolvPeopleAssigner();
        $job->setEntityManager($entity_manager);

        $job->assignSolvPeople();

        $this->assertSame([
            'INFO Person not exactly matched:',
            'INFO {}',
            <<<'ZZZZZZZZZZ'
            INFO Closest matches (difference 1): [
                {
                    "person": 1,
                    "name": "Test Runner",
                    "birth_year": "08",
                    "domicile": "Z\u00fcrich ZH"
                }
            ]
            ZZZZZZZZZZ,
            'INFO  => Matching person found: 1.',
            'INFO Person not exactly matched:',
            'INFO {}',
            <<<'ZZZZZZZZZZ'
            INFO Closest matches (difference 4): [
                {
                    "person": 1,
                    "name": "Test Runner",
                    "birth_year": "08",
                    "domicile": "Z\u00fcrich ZH"
                },
                {
                    "person": 2,
                    "name": "Best Runer",
                    "birth_year": "92",
                    "domicile": "Z\u00fcrich ZH"
                }
            ]
            ZZZZZZZZZZ,
            'INFO  => No matching person found (difference too high).',
            'NOTICE Unclear case. Maybe update logic?',
            'INFO Created new person (id 270):',
            'INFO {}',
        ], $this->getLogs());

        $solv_result_repo = $entity_manager->getRepository(SolvResult::class);
        $test_runner_result = $solv_result_repo->testRunnerResult;
        $this->assertSame(1, $test_runner_result->getPerson());
        $typo_result = $solv_result_repo->typoResult;
        $this->assertSame(1, $typo_result->getPerson());

        $flushed = $entity_manager->flushed_persisted;
        $this->assertSame(1, count($flushed));
        $this->assertSame('Test Winner', $flushed[0]->getName());
        $this->assertSame(null, $flushed[0]->getSameAs());
        $different_result = $solv_result_repo->differentResult;
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $different_result->getPerson());
    }
}
