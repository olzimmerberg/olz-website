<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendTelegramConfigurationReminderCommand;
use Olz\Constants\NotificationDeliveryType;
use Olz\Constants\NotificationType;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;

class TestOnlySendTelegramConfigurationReminderCommand extends SendTelegramConfigurationReminderCommand {
    /** @return array<int, array{reminder_id?: int, needs_reminder?: bool}> */
    public function testOnlyGetTelegramConfigReminderState(): array {
        return $this->getTelegramConfigReminderState();
    }

    /** @return array<NotificationType> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendTelegramConfigurationReminderCommand
 */
final class SendTelegramConfigurationReminderCommandTest extends UnitTestCase {
    public function testSendTelegramConfigurationReminderCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new SendTelegramConfigurationReminderCommand();
        $the_day = SendTelegramConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $job->setDateUtils(new DateUtils("2020-03-{$the_day_str} 19:00:00"));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendTelegramConfigurationReminderCommand...',
            'INFO Generating telegram configuration reminder subscriptions...',
            'INFO Removing telegram configuration reminder subscription (19) for \'admin (User ID: 2)\'...',
            'INFO Generating telegram configuration reminder subscription for \'default (User ID: 1)\'...',
            'INFO Generating telegram configuration reminder subscription for \'vorstand (User ID: 3)\'...',
            'INFO Sending \'telegram_config_reminder\' notifications...',
            'INFO Getting notifications for \'{"cancelled":false}\'...',
            'INFO Sending notification Keine Push-Nachrichten abonniert over telegram to user (2)...',
            'INFO Telegram sent to user (2): Keine Push-Nachrichten abonniert',
            'INFO Getting notifications for \'{"cancelled":true}\'...',
            'INFO Nothing to send.',
            'INFO Successfully ran command Olz\Command\Notifications\SendTelegramConfigurationReminderCommand.',
        ], $this->getLogs());
    }

    public function testSendTelegramConfigurationReminderCommandGetTelegramConfigReminderState(): void {
        $job = new TestOnlySendTelegramConfigurationReminderCommand();

        $result = $job->testOnlyGetTelegramConfigReminderState();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([
            2 => ['reminder_id' => 19],
            1 => ['needs_reminder' => true],
            3 => ['needs_reminder' => true],
        ], $result);
    }

    public function testSendTelegramConfigurationReminderCommandAutogenerateSubscriptions(): void {
        $job = new SendTelegramConfigurationReminderCommand();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Generating telegram configuration reminder subscriptions...",
            'INFO Removing telegram configuration reminder subscription (19) for \'admin (User ID: 2)\'...',
            'INFO Generating telegram configuration reminder subscription for \'default (User ID: 1)\'...',
            'INFO Generating telegram configuration reminder subscription for \'vorstand (User ID: 3)\'...',
        ], $this->getLogs());
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'default (User ID: 1)',
                NotificationDeliveryType::TELEGRAM,
                NotificationType::TELEGRAM_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
            [
                'vorstand (User ID: 3)',
                NotificationDeliveryType::TELEGRAM,
                NotificationType::TELEGRAM_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
        ], array_map(
            function ($notification_subscription) {
                return [
                    $notification_subscription->getUser()->__toString(),
                    $notification_subscription->getDeliveryType(),
                    $notification_subscription->getNotificationType(),
                    $notification_subscription->getNotificationTypeArgs(),
                ];
            },
            $entity_manager->persisted
        ));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $this->assertSame([
            [
                'admin (User ID: 2)',
                NotificationDeliveryType::TELEGRAM,
                NotificationType::TELEGRAM_CONFIG_REMINDER,
                '{"cancelled":true}',
            ],
        ], array_map(
            function ($notification_subscription) {
                return [
                    $notification_subscription->getUser()->__toString(),
                    $notification_subscription->getDeliveryType(),
                    $notification_subscription->getNotificationType(),
                    $notification_subscription->getNotificationTypeArgs(),
                ];
            },
            $entity_manager->removed
        ));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    // ---

    public function testSendTelegramConfigurationReminderCommandOnWrongDay(): void {
        $not_the_day = SendTelegramConfigurationReminderCommand::DAY_OF_MONTH + 1;
        $not_the_day_str = str_pad("{$not_the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-03-{$not_the_day_str} 19:30:00");

        $job = new SendTelegramConfigurationReminderCommand();
        $job->setDateUtils($date_utils);

        $notifications = $job->getNotifications(['cancelled' => false]);

        $this->assertSame([], $notifications);
    }

    public function testSendTelegramConfigurationReminderCommandCancelled(): void {
        $the_day = SendTelegramConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-03-{$the_day_str} 19:00:00");

        $job = new SendTelegramConfigurationReminderCommand();
        $job->setDateUtils($date_utils);

        $notifications = $job->getNotifications(['cancelled' => true]);

        $this->assertSame([], $notifications);
    }

    public function testSendTelegramConfigurationReminderCommandNotification(): void {
        $the_day = SendTelegramConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-03-{$the_day_str} 19:00:00");
        $user = FakeUser::defaultUser();

        $job = new SendTelegramConfigurationReminderCommand();
        $job->setDateUtils($date_utils);

        $notifications = $job->getNotifications(['cancelled' => false]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.


            **Du möchtest eigentlich Push-Nachrichten erhalten?**

            In diesem Fall musst du dich auf der Website *einloggen*, und im ["Newsletter"-App](http://fake-base-url/_/apps/newsletter) (ist auch unter "Service" zu finden) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


            **Du möchtest gar keine Push-Nachrichten erhalten?**

            Dann lösche einfach diesen Chat.


            ZZZZZZZZZZ;
        $this->assertCount(1, $notifications);
        $this->assertSame('Keine Push-Nachrichten abonniert', $notifications[0]->title);
        $this->assertSame($expected_text, $notifications[0]->getTextForUser($user));
    }
}
