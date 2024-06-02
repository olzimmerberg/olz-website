<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Snippets;

use Olz\Entity\Snippets\Snippet;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<Snippet>
 */
class FakeSnippetRepository extends FakeOlzRepository {
    public string $olzEntityClass = Snippet::class;
    public string $fakeOlzEntityClass = FakeSnippet::class;
}
