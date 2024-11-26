<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\NotificationGetterTrait;
use Olz\Entity\NotificationSubscription;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class NotificationGetterTraitConcreteEntity {
    use NotificationGetterTrait;

    /** @return array<string> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\NotificationGetterTrait
 */
final class NotificationGetterTraitTest extends UnitTestCase {
    public function testGetNonReminderNotificationTypes(): void {
        $notification_getter = new NotificationGetterTraitConcreteEntity();

        $this->assertSame(
            [
                NotificationSubscription::TYPE_DAILY_SUMMARY,
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                NotificationSubscription::TYPE_IMMEDIATE,
                NotificationSubscription::TYPE_MONTHLY_PREVIEW,
                NotificationSubscription::TYPE_WEEKLY_PREVIEW,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
            ],
            $notification_getter->testOnlyGetNonReminderNotificationTypes()
        );
    }
}
