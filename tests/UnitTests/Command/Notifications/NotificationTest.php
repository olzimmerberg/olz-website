<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\Notification;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\Notification
 */
final class NotificationTest extends UnitTestCase {
    public function testNotification(): void {
        $user = FakeUser::defaultUser();

        $notification = new Notification('Test title', 'Hallo %%userFirstName%% %%userLastName%%');

        $this->assertSame('Test title', $notification->title);
        $this->assertSame("Hallo Default User", $notification->getTextForUser($user));
    }
}
