<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminNotificationTemplate;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminNotificationTemplate>
 */
class FakeTerminNotificationTemplateRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminNotificationTemplate::class;
    public string $fakeOlzEntityClass = FakeTerminNotificationTemplate::class;
}
