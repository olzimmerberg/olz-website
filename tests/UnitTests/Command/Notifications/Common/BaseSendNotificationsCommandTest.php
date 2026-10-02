<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications\Common;

use Olz\Command\Notifications\Common\BaseSendNotificationsCommand;
use Olz\Constants\NotificationType;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;

class TestOnlyBaseSendNotificationsCommand extends BaseSendNotificationsCommand {
    public function getNotificationSubscriptionType(): NotificationType {
        return NotificationType::IMMEDIATE;
    }

    public function autogenerateSubscriptions(): void {
    }

    public function getNotifications(array $args): array {
        return [];
    }

    /** @return array<NotificationType> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\Common\BaseSendNotificationsCommand
 */
final class BaseSendNotificationsCommandTest extends UnitTestCase {
    public function testGetNonReminderNotificationTypes(): void {
        $command = new TestOnlyBaseSendNotificationsCommand();

        $this->assertSame(
            [
                NotificationType::DAILY_SUMMARY,
                NotificationType::DEADLINE_WARNING,
                NotificationType::IMMEDIATE,
                NotificationType::MONTHLY_PREVIEW,
                NotificationType::TERMIN_NOTIFICATION,
                NotificationType::WEEKLY_PREVIEW,
                NotificationType::WEEKLY_SUMMARY,
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
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\Notifications\\Common\\TestOnlyBaseSendNotificationsCommand...",
            "INFO Sending 'immediate' notifications...",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\Notifications\\Common\\TestOnlyBaseSendNotificationsCommand.",
        ], $this->getLogs());

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame([], $entity_manager->removed);
        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }
}
