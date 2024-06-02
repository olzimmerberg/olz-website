<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\News;

use Olz\Entity\News\NewsEntry;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<NewsEntry>
 */
class FakeNewsRepository extends FakeOlzRepository {
    public string $olzEntityClass = NewsEntry::class;
    public string $fakeOlzEntityClass = FakeNews::class;
}
