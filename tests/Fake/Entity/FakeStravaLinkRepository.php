<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\StravaLink;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<StravaLink>
 */
class FakeStravaLinkRepository extends FakeOlzRepository {
    public string $olzEntityClass = StravaLink::class;
    public string $fakeOlzEntityClass = FakeStravaLink::class;
}
