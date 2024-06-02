<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLocation;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminLocation>
 */
class FakeTerminLocationRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminLocation::class;
    public string $fakeOlzEntityClass = FakeTerminLocation::class;
}
