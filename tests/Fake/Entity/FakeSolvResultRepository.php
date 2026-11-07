<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvResult;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<SolvResult>
 */
class FakeSolvResultRepository extends FakeOlzRepository {
    public string $olzEntityClass = SolvResult::class;
    public string $fakeOlzEntityClass = FakeSolvResult::class;

    /** @return array<SolvResult> */
    public function findAll(): array {
        return [FakeSolvResult::defaultSolvResult()];
    }
}
