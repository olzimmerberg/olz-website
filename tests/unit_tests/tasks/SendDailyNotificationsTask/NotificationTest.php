<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/Notification.php';

/**
 * @internal
 * @covers \Notification
 */
final class NotificationTest extends TestCase {
    public function testNotification(): void {
        $user = new User();
        $user->setFirstName('First');
        $user->setLastName('Last');

        $notification = new Notification('Test title', 'Hallo %%userFirstName%% %%userLastName%%');

        $this->assertSame('Test title', $notification->title);
        $this->assertSame("Hallo First Last", $notification->getTextForUser($user));
    }
}
