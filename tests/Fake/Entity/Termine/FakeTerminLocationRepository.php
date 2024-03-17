<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeTerminLocationRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeTerminLocation::class;
}
