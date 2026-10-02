<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendTerminNotificationCommand;
use Olz\Constants\NotificationDeliveryType;
use Olz\Constants\NotificationType;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;

class TestOnlySendTerminNotificationCommand extends SendTerminNotificationCommand {
    /** @return array<int, array<NotificationDeliveryType, array{subscription_id?: int, needs_subscription?: bool}>> */
    public function testOnlyGetTerminNotificationState(): array {
        return $this->getTerminNotificationState();
    }

    /** @return array<NotificationType> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendTerminNotificationCommand
 */
final class SendTerminNotificationCommandTest extends UnitTestCase {
    public function testSendTerminNotificationCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new SendTerminNotificationCommand();
        $job->setDateUtils(new DateUtils("2020-03-12 19:00:00"));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendTerminNotificationCommand...',
            "INFO Generating termin notification subscriptions...",
            "INFO Removing termin notification subscription (14) for 'admin (User ID: 2)' via 'telegram'...",
            "INFO Generating termin notification subscription for 'admin (User ID: 2)' via 'email'...",
            "INFO Generating termin notification subscription for 'vorstand (User ID: 3)' via 'telegram'...",
            "INFO Generating termin notification subscription for 'default (User ID: 1)' via 'telegram'...",
            "INFO Sending 'termin_notification' notifications...",
            "INFO Getting notifications for '{\"cancelled\":false}'...",
            "ERROR Error running command Olz\\Command\\Notifications\\SendTerminNotificationCommand: Fake throttling not set up for send-termin-notifications.",
        ], $this->getLogs());
    }

    public function testSendTerminNotificationCommandGetTerminNotificationState(): void {
        $job = new TestOnlySendTerminNotificationCommand();

        $result = $job->testOnlyGetTerminNotificationState();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([
            2 => [
                'telegram' => ['subscription_id' => 14],
                'email' => ['needs_subscription' => true],
            ],
            3 => [
                'email' => ['subscription_id' => 15, 'needs_subscription' => true],
                'telegram' => ['needs_subscription' => true],
            ],
            1 => [
                'telegram' => ['needs_subscription' => true],
            ],
        ], $result);
    }

    public function testSendTerminNotificationCommandAutogenerateSubscriptions(): void {
        $job = new SendTerminNotificationCommand();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Generating termin notification subscriptions...",
            "INFO Removing termin notification subscription (14) for 'admin (User ID: 2)' via 'telegram'...",
            "INFO Generating termin notification subscription for 'admin (User ID: 2)' via 'email'...",
            "INFO Generating termin notification subscription for 'vorstand (User ID: 3)' via 'telegram'...",
            "INFO Generating termin notification subscription for 'default (User ID: 1)' via 'telegram'...",
        ], $this->getLogs());
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'admin (User ID: 2)',
                NotificationDeliveryType::EMAIL,
                NotificationType::TERMIN_NOTIFICATION,
                '{"cancelled":false}',
            ],
            [
                'vorstand (User ID: 3)',
                NotificationDeliveryType::TELEGRAM,
                NotificationType::TERMIN_NOTIFICATION,
                '{"cancelled":false}',
            ],
            [
                'default (User ID: 1)',
                NotificationDeliveryType::TELEGRAM,
                NotificationType::TERMIN_NOTIFICATION,
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
                NotificationType::TERMIN_NOTIFICATION,
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
            $entity_manager->removed
        ));
        $this->assertSame($entity_manager->removed, $entity_manager->flushed_removed);
    }

    // ---

    // TODO: Fake createNativeQuery in order to test the notification
    // public function testSendTerminNotificationCommand(): void {
    //     $entity_manager = WithUtilsCache::get('entityManager');
    //     $throttling_repo = $entity_manager->repositories[Throttling::class];
    //     $throttling_repo->last_occurrences = [
    //         'send-termin-notifications' => '2020-03-12 19:30:00',
    //     ];
    //     $entity_manager->mock_response_for_native_query = new NativeQuery($entity_manager);
    //     $date_utils = new DateUtils("2020-03-13 19:30:00");

    //     $job = new SendTerminNotificationCommand();
    //     $job->setDateUtils($date_utils);

    //     $notifications = $job->getNotifications(['cancelled' => false]);

    //     $this->assertSame([], $notifications);
    // }
}
