<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminNotification;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<TerminNotification>
 */
class FakeTerminNotificationRepository extends FakeOlzRepository {
    public string $olzEntityClass = TerminNotification::class;
    public string $fakeOlzEntityClass = FakeTerminNotification::class;
}
