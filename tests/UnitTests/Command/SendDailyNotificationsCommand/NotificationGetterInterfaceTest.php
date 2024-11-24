<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Command\SendDailyNotificationsCommand\NotificationGetterInterface;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsTrait;

class ConcreteNotificationGetter implements NotificationGetterInterface {
    use WithUtilsTrait;

    public function getNotification(array $args): ?Notification {
        return null;
    }
}

/**
 * @internal
 *
 * @coversNothing
 */
final class NotificationGetterInterfaceTest extends UnitTestCase {
    public function testNotification(): void {
        $notification_getter = new ConcreteNotificationGetter();
        $this->assertNull($notification_getter->getNotification([]));
    }
}
