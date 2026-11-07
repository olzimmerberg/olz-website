<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvPerson;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<SolvPerson>
 */
class FakeSolvPersonRepository extends FakeOlzRepository {
    public string $olzEntityClass = SolvPerson::class;
    public string $fakeOlzEntityClass = FakeSolvPerson::class;

    /** @return array<SolvPerson> */
    public function findAll(): array {
        return [FakeSolvPerson::defaultSolvPerson()];
    }
}
