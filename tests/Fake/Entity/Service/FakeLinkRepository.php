<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Entity\Service\Link;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Link>
 */
class FakeLinkRepository extends FakeOlzRepository {
    public string $olzEntityClass = Link::class;
    public string $fakeOlzEntityClass = FakeLink::class;
}
