<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Karten;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeKarteRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeKarte::class;
}
