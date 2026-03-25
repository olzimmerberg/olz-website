<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminReaction;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminReaction>
 */
class FakeTerminReactionRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminReaction::class;
    public string $fakeOlzEntityClass = FakeTerminReaction::class;
}
