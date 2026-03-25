<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\News;

use Olz\Entity\News\NewsReaction;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<NewsReaction>
 */
class FakeNewsReactionRepository extends FakeOlzRepository {
    public string $olzEntityClass = NewsReaction::class;
    public string $fakeOlzEntityClass = FakeNewsReaction::class;
}
