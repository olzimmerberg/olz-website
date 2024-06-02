<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\NotificationSubscription;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

/**
 * @extends FakeOlzRepository<NotificationSubscription>
 */
class FakeNotificationSubscriptionRepository extends FakeOlzRepository {
    public string $olzEntityClass = NotificationSubscription::class;
    public string $fakeOlzEntityClass = FakeNotificationSubscription::class;
}
