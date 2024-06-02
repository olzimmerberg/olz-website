<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminTemplate;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminTemplate>
 */
class FakeTerminTemplateRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminTemplate::class;
    public string $fakeOlzEntityClass = FakeTerminTemplate::class;
}
