<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SyncSolvAssignPeopleCommand;
use Olz\Entity\SolvResult;
use Olz\Tests\Fake;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeSolvResult;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeSyncSolvAssignPeopleCommandSolvResultRepository extends FakeOlzRepository {
    public $testRunnerResult;
    public $typoResult;
    public $differentResult;

    public function __construct($em) {
        parent::__construct($em);
        $test_runner_result = FakeSolvResult::defaultSolvResult(true);
        $test_runner_result->setId(1);
        $this->testRunnerResult = $test_runner_result;

        $typo_result = FakeSolvResult::defaultSolvResult(true);
        $typo_result->setId(2);
        $typo_result->setName('Test Runer');
        $this->typoResult = $typo_result;

        $different_result = FakeSolvResult::defaultSolvResult(true);
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
 * @covers \Olz\Command\SyncSolvAssignPeopleCommand
 */
final class SyncSolvAssignPeopleCommandTest extends UnitTestCase {
    public function testGetDifferenceBetweenPersonInfo(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvResult::class] = new FakeSyncSolvAssignPeopleCommandSolvResultRepository($entity_manager);

        $job = new SyncSolvAssignPeopleCommand();

        $this->assertSame(0, $job->getDifferenceBetweenPersonInfo(
            'Test',
            '07',
            'Uster',
            'Test',
            '07',
            'Uster',
        ));
        $this->assertSame(1, $job->getDifferenceBetweenPersonInfo(
            'Best',
            '07',
            'Uster',
            'Test',
            '07',
            'Uster',
        ));
        $this->assertSame(3, $job->getDifferenceBetweenPersonInfo(
            'Basti',
            '07',
            'Uster',
            'Test',
            '07',
            'Uster',
        ));
        $this->assertSame(0, $job->getDifferenceBetweenPersonInfo(
            'Test',
            '07',
            'Uster',
            'Test',
            7,
            'Uster',
        ));
        $this->assertSame(1, $job->getDifferenceBetweenPersonInfo(
            'Test',
            '07',
            'Uster',
            'Test',
            '08',
            'Uster',
        ));
        $this->assertSame(2, $job->getDifferenceBetweenPersonInfo(
            'Test',
            '07',
            'Uster',
            'Test',
            '07',
            'Muster',
        ));
        $this->assertSame(7, $job->getDifferenceBetweenPersonInfo(
            'Test',
            '07',
            'Thalwil',
            'Test',
            '07',
            'Muster',
        ));
        $this->assertSame(2, $job->getDifferenceBetweenPersonInfo(
            'Test',
            '07',
            'Thalwil',
            'Test',
            '07',
            '',
        ));

        $this->assertSame([
        ], $this->getLogs());
    }

    public function testGetClosestMatchesOfPersonInfo(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvResult::class] = new FakeSyncSolvAssignPeopleCommandSolvResultRepository($entity_manager);

        $job = new SyncSolvAssignPeopleCommand();

        $this->assertSame([
            'difference' => 0,
            'matches' => [
                ['name' => 'Test', 'birth_year' => '07', 'domicile' => 'Uster'],
            ],
        ], $job->getClosestMatchesOfPersonInfo(
            'Test',
            '07',
            'Uster',
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
            'Test',
            '07',
            'Uster',
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
            'Test',
            '07',
            'Uster',
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
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvResult::class] = new FakeSyncSolvAssignPeopleCommandSolvResultRepository($entity_manager);

        $job = new SyncSolvAssignPeopleCommand();

        $this->assertNull($job->getUnambiguousPerson([
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
        $this->assertNull($job->getUnambiguousPerson([]));

        $this->assertSame([
        ], $this->getLogs());
    }

    public function testGetMatchingPerson(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvResult::class] = new FakeSyncSolvAssignPeopleCommandSolvResultRepository($entity_manager);

        $job = new SyncSolvAssignPeopleCommand();

        // There is one perfect match.
        $this->assertSame(2, $job->getMatchingPerson(
            'Test',
            '07',
            'Uster',
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
        $this->resetLogs();

        // There are multiple matches for the same person.
        $this->assertSame(3, $job->getMatchingPerson(
            'Test',
            '07',
            'Uster',
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
        $this->resetLogs();

        // There are matches, but they are for different persons.
        $this->assertNull($job->getMatchingPerson(
            'Test',
            '07',
            'Uster',
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
        $this->resetLogs();

        // The matches are too bad.
        $this->assertNull($job->getMatchingPerson(
            'Test',
            '07',
            'Uster',
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
        $this->resetLogs();
    }

    public function testSyncSolvAssignPeopleCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvResult::class] = new FakeSyncSolvAssignPeopleCommandSolvResultRepository($entity_manager);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SyncSolvAssignPeopleCommand();
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\SyncSolvAssignPeopleCommand...',
            "INFO \n---\n",
            <<<'ZZZZZZZZZZ'
                INFO Person not exactly matched: SolvResult(
                    id:2,
                    event:1,
                    class:H12,
                    person:,
                    name:Test Runer,
                    birth_year:08,
                    domicile:Zürich ZH,
                    club:OL Zimmerberg,
                )
                ZZZZZZZZZZ,
            'INFO getAllAssignedSolvResultPersonData took 1234ms',
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
            'INFO getMatchingPerson took 1234ms',
            "INFO \n---\n",
            <<<'ZZZZZZZZZZ'
                INFO Person not exactly matched: SolvResult(
                    id:3,
                    event:1,
                    class:H12,
                    person:,
                    name:Test Winner,
                    birth_year:92,
                    domicile:Zürich ZH,
                    club:OL Zimmerberg,
                )
                ZZZZZZZZZZ,
            'INFO getAllAssignedSolvResultPersonData took 1234ms',
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
            'INFO getMatchingPerson took 1234ms',
            'INFO forceFlush took 1234ms',
            'INFO Created new person (id 270):',
            'INFO {}',
            'INFO forceFlush took 1234ms',
            'INFO Successfully ran command Olz\Command\SyncSolvAssignPeopleCommand.',
        ], $this->getLogs());

        $solv_result_repo = $entity_manager->getRepository(SolvResult::class);
        $test_runner_result = $solv_result_repo->testRunnerResult;
        $this->assertSame(1, $test_runner_result->getPerson());
        $typo_result = $solv_result_repo->typoResult;
        $this->assertSame(1, $typo_result->getPerson());

        $flushed = $entity_manager->flushed_persisted;
        $this->assertCount(1, $flushed);
        $this->assertSame('Test Winner', $flushed[0]->getName());
        $this->assertNull($flushed[0]->getSameAs());
        $different_result = $solv_result_repo->differentResult;
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $different_result->getPerson());
    }
}
