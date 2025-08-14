<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLabel;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminLabel>
 */
class FakeTerminLabelRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminLabel::class;
    public string $fakeOlzEntityClass = FakeTerminLabel::class;

    /** @return array<TerminLabel> */
    public function findAll(): array {
        return [
            FakeTerminLabel::minimal(),
            FakeTerminLabel::empty(),
            FakeTerminLabel::maximal(),
            FakeTerminLabel::weekend(),
            FakeTerminLabel::training(),
            FakeTerminLabel::ol(),
            FakeTerminLabel::club(),
        ];
    }
}
