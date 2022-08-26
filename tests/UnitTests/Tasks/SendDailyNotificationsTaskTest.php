<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Tasks;

use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask;
use Olz\Tasks\SendDailyNotificationsTask\Notification;
use Olz\Tests\Fake\FakeEmailUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeLogger;
use Olz\Tests\Fake\FakeTelegramUtils;
use Olz\Tests\Fake\FakeUserRepository;
use Olz\Tests\Fake\FakeUsers;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../Fake/fake_notification_subscription.php';

$user1 = FakeUsers::defaultUser(true);
$user1->setId(1);
$user1->setFirstName('First');
$user1->setLastName('User');

$user2 = FakeUsers::defaultUser(true);
$user2->setId(2);
$user2->setFirstName('Second');
$user2->setLastName('User');

$user3 = FakeUsers::defaultUser(true);
$user3->setId(3);
$user3->setFirstName('Third');
$user3->setLastName('User');

$user_provoke_error = FakeUsers::defaultUser(true);
$user_provoke_error->setId(3);
$user_provoke_error->setFirstName('Provoke');
$user_provoke_error->setLastName('Error');

$all_notification_subscriptions = [
    get_fake_notification_subscription(
        1,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        json_encode([]),
    ),
    get_fake_notification_subscription(
        2,
        NotificationSubscription::DELIVERY_EMAIL,
        $user2,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        json_encode(['no_notification' => true]),
    ),
    get_fake_notification_subscription(
        3,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user1,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        json_encode([]),
    ),
    get_fake_notification_subscription(
        4,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user1,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        json_encode(['no_notification' => true]),
    ),
    get_fake_notification_subscription(
        5,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user1,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        json_encode(['days' => 7]),
    ),
    get_fake_notification_subscription(
        6,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user2,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        json_encode(['days' => 3]),
    ),
    get_fake_notification_subscription(
        7,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user3,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        json_encode(['days' => 3]),
    ),
    get_fake_notification_subscription(
        8,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        json_encode(['days' => 3]),
    ),
    get_fake_notification_subscription(
        9,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        json_encode(['no_notification' => true]),
    ),
    get_fake_notification_subscription(
        10,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
    ),
    get_fake_notification_subscription(
        11,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        json_encode(['no_notification' => true]),
    ),
    get_fake_notification_subscription(
        12,
        NotificationSubscription::DELIVERY_EMAIL,
        $user2,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
    ),
    get_fake_notification_subscription(
        13,
        NotificationSubscription::DELIVERY_EMAIL,
        $user2,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        json_encode(['no_notification' => true]),
    ),
    get_fake_notification_subscription(
        14,
        'invalid-delivery',
        $user2,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
    ),
    get_fake_notification_subscription(
        15,
        NotificationSubscription::DELIVERY_EMAIL,
        $user2,
        'invalid-type',
        json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
    ),
    get_fake_notification_subscription(
        16,
        NotificationSubscription::DELIVERY_EMAIL,
        $user2,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        json_encode(['provoke_error' => true]),
    ),
    get_fake_notification_subscription(
        17,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user_provoke_error,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
    ),
    get_fake_notification_subscription(
        18,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user2,
        NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        json_encode(['cancelled' => false]),
    ),
    get_fake_notification_subscription(
        19,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user2,
        NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        json_encode(['cancelled' => true]),
    ),
    get_fake_notification_subscription(
        20,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        json_encode(['cancelled' => false]),
    ),
    get_fake_notification_subscription(
        21,
        NotificationSubscription::DELIVERY_EMAIL,
        $user1,
        NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        json_encode(['cancelled' => true]),
    ),
];

class FakeSendDailyNotificationsTaskNotificationSubscriptionRepository {
    public function findAll() {
        global $all_notification_subscriptions;
        return $all_notification_subscriptions;
    }

    public function findBy($where) {
        global $user1, $user2, $user3, $user_provoke_error, $all_notification_subscriptions;

        if ($where === ['notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER]) {
            return [
                get_fake_notification_subscription(
                    1,
                    NotificationSubscription::DELIVERY_EMAIL,
                    $user1,
                    NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                    json_encode(['cancelled' => false]),
                ),
                get_fake_notification_subscription(
                    2,
                    NotificationSubscription::DELIVERY_EMAIL,
                    $user2,
                    NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                    json_encode(['cancelled' => false]),
                ),
            ];
        }

        if ($where === [
            'user' => FakeUsers::defaultUser(),
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]) {
            return [
                get_fake_notification_subscription(
                    1,
                    NotificationSubscription::DELIVERY_EMAIL,
                    $user1,
                    NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                    json_encode(['cancelled' => false]),
                ),
            ];
        }

        if ($where === ['notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER]) {
            return [
                get_fake_notification_subscription(
                    1,
                    NotificationSubscription::DELIVERY_TELEGRAM,
                    $user1,
                    NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
                    json_encode(['cancelled' => false]),
                ),
                get_fake_notification_subscription(
                    2,
                    NotificationSubscription::DELIVERY_TELEGRAM,
                    $user2,
                    NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
                    json_encode(['cancelled' => false]),
                ),
            ];
        }

        if ($where === [
            'user' => FakeUsers::defaultUser(),
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]) {
            return [
                get_fake_notification_subscription(
                    1,
                    NotificationSubscription::DELIVERY_TELEGRAM,
                    $user1,
                    NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
                    json_encode(['cancelled' => false]),
                ),
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

class FakeSendDailyNotificationsTaskTelegramLinkRepository {
    public function findOneBy($where) {
        global $user1, $user2, $user3;
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
        return null;
    }

    public function getActivatedTelegramLinks() {
        global $user1, $user2, $user3;

        $telegram_link_1 = new TelegramLink();
        $telegram_link_1->setUser($user1);
        $telegram_link_1->setTelegramChatId('11111');

        $telegram_link_2 = new TelegramLink();
        $telegram_link_2->setUser($user2);
        $telegram_link_2->setTelegramChatId('22222');

        $telegram_link_3 = new TelegramLink();
        $telegram_link_3->setUser($user3);
        $telegram_link_3->setTelegramChatId('33333');

        return [$telegram_link_1, $telegram_link_2, $telegram_link_3];
    }
}

class FakeSendDailyNotificationsTaskDailySummaryGetter {
    use \Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getDailySummaryNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('DS title', 'DS text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsTaskDeadlineWarningGetter {
    use \Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

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
    use \Psr\Log\LoggerAwareTrait;

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("ECR title {$args_str}", 'ECR text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsTaskMonthlyPreviewGetter {
    use \Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getMonthlyPreviewNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('MP title', 'MP text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsTelegramConfigurationReminderGetter {
    use \Psr\Log\LoggerAwareTrait;

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("TCR title {$args_str}", 'TCR text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsTaskWeeklyPreviewGetter {
    use \Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function getWeeklyPreviewNotification($args) {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('WP title', 'WP text %%userFirstName%%');
    }
}

class FakeSendDailyNotificationsTaskWeeklySummaryGetter {
    use \Psr\Log\LoggerAwareTrait;

    public function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function setDateUtils($dateUtils) {
        $this->dateUtils = $dateUtils;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

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
 * @covers \Olz\Tasks\SendDailyNotificationsTask
 */
final class SendDailyNotificationsTaskTest extends UnitTestCase {
    public function testSendDailyNotificationsTask(): void {
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeSendDailyNotificationsTaskNotificationSubscriptionRepository();
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $telegram_link_repo = new FakeSendDailyNotificationsTaskTelegramLinkRepository();
        $entity_manager->repositories[TelegramLink::class] = $telegram_link_repo;
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $telegram_utils = new FakeTelegramUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();
        $daily_summary_getter = new FakeSendDailyNotificationsTaskDailySummaryGetter();
        $deadline_warning_getter = new FakeSendDailyNotificationsTaskDeadlineWarningGetter();
        $email_configuration_reminder_getter = new FakeSendDailyNotificationsEmailConfigurationReminderGetter();
        $monthly_preview_getter = new FakeSendDailyNotificationsTaskMonthlyPreviewGetter();
        $telegram_configuration_reminder_getter = new FakeSendDailyNotificationsTelegramConfigurationReminderGetter();
        $weekly_preview_getter = new FakeSendDailyNotificationsTaskWeeklyPreviewGetter();
        $weekly_summary_getter = new FakeSendDailyNotificationsTaskWeeklySummaryGetter();

        $job = new SendDailyNotificationsTask($entity_manager, $email_utils, $telegram_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->setDailySummaryGetter($daily_summary_getter);
        $job->setDeadlineWarningGetter($deadline_warning_getter);
        $job->setEmailConfigurationReminderGetter($email_configuration_reminder_getter);
        $job->setMonthlyPreviewGetter($monthly_preview_getter);
        $job->setTelegramConfigurationReminderGetter($telegram_configuration_reminder_getter);
        $job->setWeeklyPreviewGetter($weekly_preview_getter);
        $job->setWeeklySummaryGetter($weekly_summary_getter);
        $job->run();

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
            [$user1, '[OLZ] MP title', 'MP text First'],
            [$user1, '[OLZ] DW title {"days":3}', 'DW text First'],
            [$user1, '[OLZ] DS title', 'DS text First'],
            [$user2, '[OLZ] WS title', 'WS text Second'],
            [$user1, '[OLZ] ECR title {"cancelled":false}', 'ECR text First'],
        ], $email_utils->olzMailer->emails_sent);
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
        ], $telegram_utils->telegramApiCalls);
        $this->assertSame($entity_manager, $daily_summary_getter->entityManager);
        $this->assertSame($date_utils, $daily_summary_getter->dateUtils);
        $this->assertSame($env_utils, $daily_summary_getter->envUtils);
        $this->assertSame($entity_manager, $deadline_warning_getter->entityManager);
        $this->assertSame($date_utils, $deadline_warning_getter->dateUtils);
        $this->assertSame($env_utils, $deadline_warning_getter->envUtils);
        $this->assertSame($entity_manager, $monthly_preview_getter->entityManager);
        $this->assertSame($date_utils, $monthly_preview_getter->dateUtils);
        $this->assertSame($env_utils, $monthly_preview_getter->envUtils);
        $this->assertSame($date_utils, $telegram_configuration_reminder_getter->dateUtils);
        $this->assertSame($env_utils, $telegram_configuration_reminder_getter->envUtils);
        $this->assertSame($entity_manager, $weekly_preview_getter->entityManager);
        $this->assertSame($date_utils, $weekly_preview_getter->dateUtils);
        $this->assertSame($env_utils, $weekly_preview_getter->envUtils);
        $this->assertSame($entity_manager, $weekly_summary_getter->entityManager);
        $this->assertSame($date_utils, $weekly_summary_getter->dateUtils);
        $this->assertSame($env_utils, $weekly_summary_getter->envUtils);
        $this->assertSame([
            "INFO Setup task SendDailyNotifications...",
            "INFO Running task SendDailyNotifications...",
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
            "NOTICE User (3) has no telegram link, but a subscription (17)",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Getting notification for '{\"provoke_error\":true}'...",
            "INFO Sending notification provoke_error over email to user (2)...",
            "CRITICAL Error sending email to user (2): [Exception] Provoked Error",
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
            "INFO Finished task SendDailyNotifications.",
            "INFO Teardown task SendDailyNotifications...",
        ], $logger->handler->getPrettyRecords());
    }
}