<?php

declare(strict_types=1);

require_once __DIR__.'/../../fake/fake_notification_subscription.php';
require_once __DIR__.'/../../fake/FakeUsers.php';
require_once __DIR__.'/../../fake/FakeEmailUtils.php';
require_once __DIR__.'/../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../fake/FakeLogger.php';
require_once __DIR__.'/../../fake/FakeTelegramUtils.php';
require_once __DIR__.'/../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../src/model/NotificationSubscription.php';
require_once __DIR__.'/../../../src/model/TelegramLink.php';
require_once __DIR__.'/../../../src/tasks/SendDailyNotificationsTask/Notification.php';
require_once __DIR__.'/../../../src/tasks/SendDailyNotificationsTask.php';
require_once __DIR__.'/../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../common/UnitTestCase.php';

$user1 = FakeUsers::defaultUser(true);
$user1->setId(1);
$user1->setFirstName('First');
$user1->setLastName('User');

$user2 = FakeUsers::defaultUser(true);
$user2->setId(2);
$user2->setFirstName('Second');
$user2->setLastName('User');

$user_provoke_error = FakeUsers::defaultUser(true);
$user_provoke_error->setId(3);
$user_provoke_error->setFirstName('Provoke');
$user_provoke_error->setLastName('Error');

class FakeSendDailyNotificationsTaskNotificationSubscriptionRepository {
    public function findBy($where) {
        global $user1, $user2, $user_provoke_error;
        return [
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user1,
                NotificationSubscription::TYPE_MONTHLY_PREVIEW,
                json_encode([]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user2,
                NotificationSubscription::TYPE_MONTHLY_PREVIEW,
                json_encode(['no_notification' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_TELEGRAM,
                $user1,
                NotificationSubscription::TYPE_WEEKLY_PREVIEW,
                json_encode([]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_TELEGRAM,
                $user1,
                NotificationSubscription::TYPE_WEEKLY_PREVIEW,
                json_encode(['no_notification' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_TELEGRAM,
                $user1,
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                json_encode(['days' => 7]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_TELEGRAM,
                $user2,
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                json_encode(['days' => 3]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user1,
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                json_encode(['days' => 3]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user1,
                NotificationSubscription::TYPE_DEADLINE_WARNING,
                json_encode(['no_notification' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user1,
                NotificationSubscription::TYPE_DAILY_SUMMARY,
                json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user1,
                NotificationSubscription::TYPE_DAILY_SUMMARY,
                json_encode(['no_notification' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user2,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
                json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user2,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
                json_encode(['no_notification' => true]),
            ),
            get_fake_notification_subscription(
                'invalid-delivery',
                $user2,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
                json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user2,
                'invalid-type',
                json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_EMAIL,
                $user2,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
                json_encode(['provoke_error' => true]),
            ),
            get_fake_notification_subscription(
                NotificationSubscription::DELIVERY_TELEGRAM,
                $user_provoke_error,
                NotificationSubscription::TYPE_WEEKLY_SUMMARY,
                json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
            ),
        ];
    }
}

class FakeSendDailyNotificationsTaskTelegramLinkRepository {
    public function findOneBy($where) {
        global $user1, $user2;
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
        return null;
    }
}

class FakeSendDailyNotificationsTaskDailySummaryGetter {
    use Psr\Log\LoggerAwareTrait;

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
    use Psr\Log\LoggerAwareTrait;

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

class FakeSendDailyNotificationsTaskMonthlyPreviewGetter {
    use Psr\Log\LoggerAwareTrait;

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

class FakeSendDailyNotificationsTaskWeeklyPreviewGetter {
    use Psr\Log\LoggerAwareTrait;

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
    use Psr\Log\LoggerAwareTrait;

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
 * @covers \SendDailyNotificationsTask
 */
final class SendDailyNotificationsTaskTest extends UnitTestCase {
    public function testSendDailyNotificationsTask(): void {
        $entity_manager = new FakeEntityManager();
        $notification_subscription_repo = new FakeSendDailyNotificationsTaskNotificationSubscriptionRepository();
        $entity_manager->repositories['NotificationSubscription'] = $notification_subscription_repo;
        $telegram_link_repo = new FakeSendDailyNotificationsTaskTelegramLinkRepository();
        $entity_manager->repositories['TelegramLink'] = $telegram_link_repo;
        $env_utils = new FakeEnvUtils();
        $email_utils = new FakeEmailUtils();
        $telegram_utils = new FakeTelegramUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = FakeLogger::create();
        $daily_summary_getter = new FakeSendDailyNotificationsTaskDailySummaryGetter();
        $deadline_warning_getter = new FakeSendDailyNotificationsTaskDeadlineWarningGetter();
        $monthly_preview_getter = new FakeSendDailyNotificationsTaskMonthlyPreviewGetter();
        $weekly_preview_getter = new FakeSendDailyNotificationsTaskWeeklyPreviewGetter();
        $weekly_summary_getter = new FakeSendDailyNotificationsTaskWeeklySummaryGetter();

        $job = new SendDailyNotificationsTask($entity_manager, $email_utils, $telegram_utils, $date_utils, $env_utils);
        $job->setLogger($logger);
        $job->setDailySummaryGetter($daily_summary_getter);
        $job->setDeadlineWarningGetter($deadline_warning_getter);
        $job->setMonthlyPreviewGetter($monthly_preview_getter);
        $job->setWeeklyPreviewGetter($weekly_preview_getter);
        $job->setWeeklySummaryGetter($weekly_summary_getter);
        $job->run();

        global $user1, $user2;
        $this->assertSame([
            [$user1, '[OLZ] MP title', 'MP text First'],
            [$user1, '[OLZ] DW title {"days":3}', 'DW text First'],
            [$user1, '[OLZ] DS title', 'DS text First'],
            [$user2, '[OLZ] WS title', 'WS text Second'],
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
        $this->assertSame($entity_manager, $weekly_preview_getter->entityManager);
        $this->assertSame($date_utils, $weekly_preview_getter->dateUtils);
        $this->assertSame($env_utils, $weekly_preview_getter->envUtils);
        $this->assertSame($entity_manager, $weekly_summary_getter->entityManager);
        $this->assertSame($date_utils, $weekly_summary_getter->dateUtils);
        $this->assertSame($env_utils, $weekly_summary_getter->envUtils);
        $this->assertSame([
            "INFO Setup task SendDailyNotifications...",
            "INFO Running task SendDailyNotifications...",
            "INFO Found notification subscription for 'monthly_preview', '[]'...",
            "INFO Found notification subscription for 'monthly_preview', '{\"no_notification\":true}'...",
            "INFO Found notification subscription for 'weekly_preview', '[]'...",
            "INFO Found notification subscription for 'weekly_preview', '{\"no_notification\":true}'...",
            "INFO Found notification subscription for 'deadline_warning', '{\"days\":7}'...",
            "INFO Found notification subscription for 'deadline_warning', '{\"days\":3}'...",
            "INFO Found notification subscription for 'deadline_warning', '{\"days\":3}'...",
            "INFO Found notification subscription for 'deadline_warning', '{\"no_notification\":true}'...",
            "INFO Found notification subscription for 'daily_summary', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Found notification subscription for 'daily_summary', '{\"no_notification\":true}'...",
            "INFO Found notification subscription for 'weekly_summary', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Found notification subscription for 'weekly_summary', '{\"no_notification\":true}'...",
            "INFO Found notification subscription for 'weekly_summary', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Found notification subscription for 'invalid-type', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Found notification subscription for 'weekly_summary', '{\"provoke_error\":true}'...",
            "INFO Found notification subscription for 'weekly_summary', '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
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
            "CRITICAL User (3) has no telegram link, but a subscription ()",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Getting notification for '{\"provoke_error\":true}'...",
            "INFO Sending notification provoke_error over email to user (2)...",
            "CRITICAL Error sending email to user (2): Provoked Error",
            "INFO Sending 'invalid-type' notifications...",
            "CRITICAL Unknown notification type 'invalid-type'",
            "INFO Finished task SendDailyNotifications.",
            "INFO Teardown task SendDailyNotifications...",
        ], $logger->handler->getPrettyRecords());
    }
}
