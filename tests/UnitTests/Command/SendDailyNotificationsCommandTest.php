<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SendDailyNotificationsCommand;
use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

$user1 = Fake\FakeUsers::defaultUser(true);
$user1->setId(1);
$user1->setFirstName('First');
$user1->setLastName('User');

$user2 = Fake\FakeUsers::defaultUser(true);
$user2->setId(2);
$user2->setFirstName('Second');
$user2->setLastName('User');

$user3 = Fake\FakeUsers::defaultUser(true);
$user3->setId(3);
$user3->setFirstName('Third');
$user3->setLastName('User');

$user_provoke_error = Fake\FakeUsers::defaultUser(true);
$user_provoke_error->setId(3);
$user_provoke_error->setFirstName('Provoke');
$user_provoke_error->setLastName('Error');

$user_no_telegram_link = Fake\FakeUsers::defaultUser(true);
$user_no_telegram_link->setId(4);
$user_no_telegram_link->setFirstName('No Telegram');
$user_no_telegram_link->setLastName('Link');

$subscription_1 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_1->setId(1);
$subscription_1->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_1->setUser($user1);
$subscription_1->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
$subscription_1->setNotificationTypeArgs(json_encode([]));
$subscription_2 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_2->setId(2);
$subscription_2->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_2->setUser($user2);
$subscription_2->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
$subscription_2->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_3 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_3->setId(3);
$subscription_3->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_3->setUser($user1);
$subscription_3->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
$subscription_3->setNotificationTypeArgs(json_encode([]));
$subscription_4 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_4->setId(4);
$subscription_4->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_4->setUser($user1);
$subscription_4->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
$subscription_4->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_5 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_5->setId(5);
$subscription_5->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_5->setUser($user1);
$subscription_5->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_5->setNotificationTypeArgs(json_encode(['days' => 7]));
$subscription_6 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_6->setId(6);
$subscription_6->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_6->setUser($user2);
$subscription_6->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_6->setNotificationTypeArgs(json_encode(['days' => 3]));
$subscription_7 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_7->setId(7);
$subscription_7->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_7->setUser($user3);
$subscription_7->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_7->setNotificationTypeArgs(json_encode(['days' => 3]));
$subscription_8 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_8->setId(8);
$subscription_8->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_8->setUser($user1);
$subscription_8->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_8->setNotificationTypeArgs(json_encode(['days' => 3]));
$subscription_9 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_9->setId(9);
$subscription_9->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_9->setUser($user1);
$subscription_9->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_9->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_10 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_10->setId(10);
$subscription_10->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_10->setUser($user1);
$subscription_10->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
$subscription_10->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_11 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_11->setId(11);
$subscription_11->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_11->setUser($user1);
$subscription_11->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
$subscription_11->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_12 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_12->setId(12);
$subscription_12->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_12->setUser($user2);
$subscription_12->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_12->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_13 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_13->setId(13);
$subscription_13->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_13->setUser($user2);
$subscription_13->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_13->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_14 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_14->setId(14);
$subscription_14->setDeliveryType('invalid-delivery');
$subscription_14->setUser($user2);
$subscription_14->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_14->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_15 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_15->setId(15);
$subscription_15->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_15->setUser($user2);
$subscription_15->setNotificationType('invalid-type');
$subscription_15->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_16 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_16->setId(16);
$subscription_16->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_16->setUser($user2);
$subscription_16->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_16->setNotificationTypeArgs(json_encode(['provoke_error' => true]));
$subscription_17 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_17->setId(17);
$subscription_17->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_17->setUser($user_provoke_error);
$subscription_17->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_17->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_18 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_18->setId(18);
$subscription_18->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_18->setUser($user2);
$subscription_18->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
$subscription_18->setNotificationTypeArgs(json_encode(['cancelled' => false]));
$subscription_19 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_19->setId(19);
$subscription_19->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_19->setUser($user2);
$subscription_19->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
$subscription_19->setNotificationTypeArgs(json_encode(['cancelled' => true]));
$subscription_20 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_20->setId(20);
$subscription_20->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_20->setUser($user1);
$subscription_20->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
$subscription_20->setNotificationTypeArgs(json_encode(['cancelled' => false]));
$subscription_21 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_21->setId(21);
$subscription_21->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_21->setUser($user1);
$subscription_21->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
$subscription_21->setNotificationTypeArgs(json_encode(['cancelled' => true]));
$subscription_22 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_22->setId(22);
$subscription_22->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_22->setUser($user_no_telegram_link);
$subscription_22->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_22->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));

$all_notification_subscriptions = [
    $subscription_1,
    $subscription_2,
    $subscription_3,
    $subscription_4,
    $subscription_5,
    $subscription_6,
    $subscription_7,
    $subscription_8,
    $subscription_9,
    $subscription_10,
    $subscription_11,
    $subscription_12,
    $subscription_13,
    $subscription_14,
    $subscription_15,
    $subscription_16,
    $subscription_17,
    $subscription_18,
    $subscription_19,
    $subscription_20,
    $subscription_21,
    $subscription_22,
];

class FakeSendDailyNotificationsCommandNotificationSubscriptionRepository {
    public function findAll() {
        global $all_notification_subscriptions;
        return $all_notification_subscriptions;
    }

    public function findBy($where) {
        global $user1, $user2, $user3, $user_provoke_error, $all_notification_subscriptions;

        if ($where === ['notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER]) {
            $subscription_31 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_31->setId(1);
            $subscription_31->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
            $subscription_31->setUser($user1);
            $subscription_31->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
            $subscription_31->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            $subscription_32 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_32->setId(2);
            $subscription_32->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
            $subscription_32->setUser($user2);
            $subscription_32->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
            $subscription_32->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            return [
                $subscription_31,
                $subscription_32,
            ];
        }

        if ($where === [
            'user' => Fake\FakeUsers::defaultUser(),
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]) {
            $subscription_41 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_41->setId(1);
            $subscription_41->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
            $subscription_41->setUser($user1);
            $subscription_41->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
            $subscription_41->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            return [
                $subscription_41,
            ];
        }

        if ($where === ['notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER]) {
            $subscription_51 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_51->setId(1);
            $subscription_51->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
            $subscription_51->setUser($user1);
            $subscription_51->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
            $subscription_51->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            $subscription_52 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_52->setId(2);
            $subscription_52->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
            $subscription_52->setUser($user2);
            $subscription_52->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
            $subscription_52->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            return [
                $subscription_51,
                $subscription_52,
            ];
        }

        if ($where === [
            'user' => Fake\FakeUsers::defaultUser(),
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]) {
            $subscription_61 = Fake\FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_61->setId(1);
            $subscription_61->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
            $subscription_61->setUser($user1);
            $subscription_61->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
            $subscription_61->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            return [
                $subscription_61,
            ];
        }

        return $all_notification_subscriptions;
    }

    public function findOneBy($where) {
        global $user1;
        if ($where['user'] === $user1) {
            return [new NotificationSubscription()];
        }
        return [];
    }
}

class FakeSendDailyNotificationsCommandTelegramLinkRepository {
    public function findOneBy($where) {
        global $user1, $user2, $user3, $user_provoke_error;
        if ($where == ['user' => $user1]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('11111');
            return $telegram_link;
        }
        if ($where == ['user' => $user2]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('22222');
            return $telegram_link;
        }
        if ($where == ['user' => $user3]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId(null);
            return $telegram_link;
        }
        if ($where == ['user' => $user_provoke_error]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('provoke_error');
            return $telegram_link;
        }
        return null;
    }

    public function getActivatedTelegramLinks() {
        global $user1, $user2, $user3, $user_provoke_error;

        $telegram_link_1 = new TelegramLink();
        $telegram_link_1->setUser($user1);
        $telegram_link_1->setTelegramChatId('11111');

        $telegram_link_2 = new TelegramLink();
        $telegram_link_2->setUser($user2);
        $telegram_link_2->setTelegramChatId('22222');

        $telegram_link_3 = new TelegramLink();
        $telegram_link_3->setUser($user3);
        $telegram_link_3->setTelegramChatId('33333');

        $telegram_link_4 = new TelegramLink();
        $telegram_link_4->setUser($user_provoke_error);
        $telegram_link_4->setTelegramChatId('provoke_error');

        return [$telegram_link_1, $telegram_link_2, $telegram_link_3, $telegram_link_4];
    }
}

class FakeSendDailyNotificationsCommandDailySummaryGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getDailySummaryNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('DS title', 'DS text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsCommandDeadlineWarningGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getDeadlineWarningNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("DW title {$args_str}", 'DW text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsEmailConfigurationReminderGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("ECR title {$args_str}", 'ECR text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsCommandMonthlyPreviewGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getMonthlyPreviewNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('MP title', 'MP text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsTelegramConfigurationReminderGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("TCR title {$args_str}", 'TCR text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsCommandWeeklyPreviewGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getWeeklyPreviewNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('WP title', 'WP text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsCommandWeeklySummaryGetter {
    use WithUtilsTrait;

    public $calledWithArgs;

    public function getWeeklySummaryNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        if ($args['provoke_error'] ?? false) {
            return new Notification('provoke_error', 'provoke_error');
        }
        return new Notification('WS title', 'WS text %%userFirstName%%');
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand
 */
final class SendDailyNotificationsCommandTest extends UnitTestCase {
    public function testSendDailyNotificationsCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $notification_subscription_repo = new FakeSendDailyNotificationsCommandNotificationSubscriptionRepository();
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $telegram_link_repo = new FakeSendDailyNotificationsCommandTelegramLinkRepository();
        $entity_manager->repositories[TelegramLink::class] = $telegram_link_repo;
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $daily_summary_getter = new FakeSendDailyNotificationsCommandDailySummaryGetter();
        $deadline_warning_getter = new FakeSendDailyNotificationsCommandDeadlineWarningGetter();
        $email_configuration_reminder_getter = new FakeSendDailyNotificationsEmailConfigurationReminderGetter();
        $monthly_preview_getter = new FakeSendDailyNotificationsCommandMonthlyPreviewGetter();
        $telegram_configuration_reminder_getter = new FakeSendDailyNotificationsTelegramConfigurationReminderGetter();
        $weekly_preview_getter = new FakeSendDailyNotificationsCommandWeeklyPreviewGetter();
        $weekly_summary_getter = new FakeSendDailyNotificationsCommandWeeklySummaryGetter();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $job = new SendDailyNotificationsCommand();
        $job->setDailySummaryGetter($daily_summary_getter);
        $job->setDeadlineWarningGetter($deadline_warning_getter);
        $job->setEmailConfigurationReminderGetter($email_configuration_reminder_getter);
        $job->setMonthlyPreviewGetter($monthly_preview_getter);
        $job->setTelegramConfigurationReminderGetter($telegram_configuration_reminder_getter);
        $job->setWeeklyPreviewGetter($weekly_preview_getter);
        $job->setWeeklySummaryGetter($weekly_summary_getter);
        $job->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Command\\SendDailyNotificationsCommand...",
            "INFO Autogenerating notifications...",
            "INFO Removing email configuration reminder subscription for 'user (ID:1)'...",
            "INFO Generating email configuration reminder subscription for 'vorstand (ID:3)'...",
            "INFO Removing telegram configuration reminder subscription for 'user (ID:1)'...",
            "INFO Generating telegram configuration reminder subscription for 'vorstand (ID:3)'...",
            "INFO Sending 'monthly_preview' notifications...",
            "INFO Getting notification for '[]'...",
            "INFO Sending notification MP title over email to user (1)...",
            "INFO Email sent to user (1): MP title",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'weekly_preview' notifications...",
            "INFO Getting notification for '[]'...",
            "INFO Sending notification WP title over telegram to user (1)...",
            "INFO Telegram sent to user (1): WP title",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'deadline_warning' notifications...",
            "INFO Getting notification for '{\"days\":7}'...",
            "INFO Sending notification DW title {\"days\":7} over telegram to user (1)...",
            "INFO Telegram sent to user (1): DW title {\"days\":7}",
            "INFO Getting notification for '{\"days\":3}'...",
            "INFO Sending notification DW title {\"days\":3} over telegram to user (2)...",
            "INFO Telegram sent to user (2): DW title {\"days\":3}",
            "INFO Sending notification DW title {\"days\":3} over telegram to user (3)...",
            "CRITICAL User (3) has a telegram link without chat ID, but a subscription (7)",
            "INFO Sending notification DW title {\"days\":3} over email to user (1)...",
            "INFO Email sent to user (1): DW title {\"days\":3}",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'daily_summary' notifications...",
            "INFO Getting notification for '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Sending notification DS title over email to user (1)...",
            "INFO Email sent to user (1): DS title",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'weekly_summary' notifications...",
            "INFO Getting notification for '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Sending notification WS title over email to user (2)...",
            "INFO Email sent to user (2): WS title",
            "INFO Sending notification WS title over invalid-delivery to user (2)...",
            "CRITICAL Unknown delivery type 'invalid-delivery'",
            "INFO Sending notification WS title over telegram to user (3)...",
            "NOTICE Error sending telegram to user (3): [Exception] provoked telegram error",
            "INFO Sending notification WS title over telegram to user (4)...",
            "NOTICE User (4) has no telegram link, but a subscription (22)",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Getting notification for '{\"provoke_error\":true}'...",
            "INFO Sending notification provoke_error over email to user (2)...",
            "CRITICAL Error sending email to user (2): [Exception] Provoked Mailer Error",
            "INFO Sending 'invalid-type' notifications...",
            "CRITICAL Unknown notification type 'invalid-type'",
            "INFO Sending 'telegram_config_reminder' notifications...",
            "INFO Getting notification for '{\"cancelled\":false}'...",
            "INFO Sending notification TCR title {\"cancelled\":false} over telegram to user (2)...",
            "INFO Telegram sent to user (2): TCR title {\"cancelled\":false}",
            "INFO Getting notification for '{\"cancelled\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'email_config_reminder' notifications...",
            "INFO Getting notification for '{\"cancelled\":false}'...",
            "INFO Sending notification ECR title {\"cancelled\":false} over email to user (1)...",
            "INFO Email sent to user (1): ECR title {\"cancelled\":false}",
            "INFO Getting notification for '{\"cancelled\":true}'...",
            "INFO Nothing to send.",
            "INFO Successfully ran command Olz\\Command\\SendDailyNotificationsCommand.",
        ], $this->getLogs());

        global $user1, $user2;
        $this->assertSame([
            [
                'vorstand (ID:3)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
            [
                'vorstand (ID:3)',
                NotificationSubscription::DELIVERY_TELEGRAM,
                NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
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
                'user (ID:1)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
            [
                'user (ID:1)',
                NotificationSubscription::DELIVERY_TELEGRAM,
                NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
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
        $this->assertSame([
            [
                'user' => $user1,
                'from' => ['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'],
                'sender' => null,
                'replyTo' => null,
                'subject' => '[OLZ] MP title',
                'body' => 'MP text First',
                'altBody' => 'MP text First',
            ],
            [
                'user' => $user1,
                'from' => ['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'],
                'sender' => null,
                'replyTo' => null,
                'subject' => '[OLZ] DW title {"days":3}',
                'body' => 'DW text First',
                'altBody' => 'DW text First',
            ],
            [
                'user' => $user1,
                'from' => ['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'],
                'sender' => null,
                'replyTo' => null,
                'subject' => '[OLZ] DS title',
                'body' => 'DS text First',
                'altBody' => 'DS text First',
            ],
            [
                'user' => $user2,
                'from' => ['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'],
                'sender' => null,
                'replyTo' => null,
                'subject' => '[OLZ] WS title',
                'body' => 'WS text Second',
                'altBody' => 'WS text Second',
            ],
            [
                'user' => $user1,
                'from' => ['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'],
                'sender' => null,
                'replyTo' => null,
                'subject' => '[OLZ] ECR title {"cancelled":false}',
                'body' => 'ECR text First',
                'altBody' => 'ECR text First',
            ],
        ], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
        $this->assertSame([
            ['sendMessage', [
                'chat_id' => '11111',
                'parse_mode' => 'HTML',
                'text' => "<b>WP title</b>\n\nWP text First",
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '11111',
                'parse_mode' => 'HTML',
                'text' => "<b>DW title {\"days\":7}</b>\n\nDW text First",
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '22222',
                'parse_mode' => 'HTML',
                'text' => "<b>DW title {\"days\":3}</b>\n\nDW text Second",
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '22222',
                'parse_mode' => 'HTML',
                'text' => "<b>TCR title {\"cancelled\":false}</b>\n\nTCR text Second",
                'disable_web_page_preview' => true,
            ]],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
        $this->assertSame($entity_manager, $daily_summary_getter->entityManager());
        $this->assertSame($entity_manager, $deadline_warning_getter->entityManager());
        $this->assertSame($entity_manager, $monthly_preview_getter->entityManager());
        $this->assertSame($entity_manager, $weekly_preview_getter->entityManager());
        $this->assertSame($entity_manager, $weekly_summary_getter->entityManager());
    }
}
