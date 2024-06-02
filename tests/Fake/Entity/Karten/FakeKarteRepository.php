<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Karten;

use Olz\Entity\Karten\Karte;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Karte>
 */
class FakeKarteRepository extends FakeOlzRepository {
    public string $olzEntityClass = Karte::class;
    public string $fakeOlzEntityClass = FakeKarte::class;
}
