<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\Notification
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
