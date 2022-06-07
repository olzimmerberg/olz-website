<?php

declare(strict_types=1);
use App\Entity\User;

require_once __DIR__.'/../../../../_/tasks/SendDailyNotificationsTask/Notification.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \Notification
 */
final class NotificationTest extends UnitTestCase {
    public function testNotification(): void {
        $user = new User();
        $user->setFirstName('First');
        $user->setLastName('Last');

        $notification = new Notification('Test title', 'Hallo %%userFirstName%% %%userLastName%%');

        $this->assertSame('Test title', $notification->title);
        $this->assertSame("Hallo First Last", $notification->getTextForUser($user));
    }
}
