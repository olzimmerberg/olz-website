<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\News;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeNewsRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeNews::class;
}
