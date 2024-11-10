<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\Counter;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Counter>
 */
class FakeCounterRepository extends FakeOlzRepository {
    public string $olzEntityClass = Counter::class;
    public string $fakeOlzEntityClass = FakeCounter::class;
}
