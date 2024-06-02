<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anmelden;

use Olz\Entity\Anmelden\Booking;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Booking>
 */
class FakeBookingRepository extends FakeOlzRepository {
    public string $olzEntityClass = Booking::class;
}
