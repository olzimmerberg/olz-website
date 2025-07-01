<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\BaseSendNotificationsCommand;
use Olz\Command\Notifications\Notification;
use Olz\Entity\NotificationSubscription;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;

class TestOnlyBaseSendNotificationsCommand extends BaseSendNotificationsCommand {
    public function getNotificationSubscriptionType(): string {
        return 'fake-type';
    }

    public function autogenerateSubscriptions(): void {
    }

    public function getNotification(array $args): ?Notification {
        return null;
    }

    /** @return array<string> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\BaseSendNotificationsCommand
 */
final class BaseSendNotificationsCommandTest extends UnitTestCase {
    public function testGetNonReminderNotificationTypes(): void {
        $command = new TestOnlyBaseSendNotificationsCommand();

        $this->assertSame(
            [
                NotificationSubscription::TYPE_DAILY_SUMMARY,
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                NotificationSubscription::TYPE_IMMEDIATE,
                NotificationSubscription::TYPE_MONTHLY_PREVIEW,
                NotificationSubscription::TYPE_WEEKLY_PREVIEW,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
            ],
            $command->testOnlyGetNonReminderNotificationTypes()
        );
    }

    public function testBaseSendNotificationsCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $command = new TestOnlyBaseSendNotificationsCommand();
        $command->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Notifications\\TestOnlyBaseSendNotificationsCommand...",
            "INFO Sending 'fake-type' notifications...",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\Notifications\\TestOnlyBaseSendNotificationsCommand.",
        ], $this->getLogs());

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame([], $entity_manager->removed);
        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }
}
