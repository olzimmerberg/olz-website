<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Snippets;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeSnippetRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeSnippet::class;
}
