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

    /** @return array<NotificationSubscription> */
    public function findAll(): array {
        return [
            FakeNotificationSubscription::subscription1(),
            FakeNotificationSubscription::subscription2(),
            FakeNotificationSubscription::subscription3(),
            FakeNotificationSubscription::subscription4(),
            FakeNotificationSubscription::subscription5(),
            FakeNotificationSubscription::subscription6(),
            FakeNotificationSubscription::subscription7(),
            FakeNotificationSubscription::subscription8(),
            FakeNotificationSubscription::subscription9(),
            FakeNotificationSubscription::subscription10(),
            FakeNotificationSubscription::subscription11(),
            FakeNotificationSubscription::subscription12(),
            FakeNotificationSubscription::subscription13(),
            FakeNotificationSubscription::subscription14(),
            FakeNotificationSubscription::subscription15(),
            FakeNotificationSubscription::subscription16(),
            FakeNotificationSubscription::subscription17(),
            FakeNotificationSubscription::subscription18(),
            FakeNotificationSubscription::subscription19(),
            FakeNotificationSubscription::subscription20(),
            FakeNotificationSubscription::subscription21(),
            FakeNotificationSubscription::subscription22(),
            FakeNotificationSubscription::subscription23(),
        ];
    }
}
