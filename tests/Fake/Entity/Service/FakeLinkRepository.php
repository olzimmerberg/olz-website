<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeLinkRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeLinks::class;
}
