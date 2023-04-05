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
use Olz\Utils\FixedDateUtils;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

require_once __DIR__.'/../../Fake/fake_notification_subscription.php';

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
    get_fake_notification_subscription(
        22,
        NotificationSubscription::DELIVERY_TELEGRAM,
        $user_no_telegram_link,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]),
    ),
];

class FakeSendDailyNotificationsCommandNotificationSubscriptionRepository {
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
            'user' => Fake\FakeUsers::defaultUser(),
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
            'user' => Fake\FakeUsers::defaultUser(),
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
    use \Psr\Log\LoggerAwareTrait;

    public $entityManager;
    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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

class FakeSendDailyNotificationsCommandDeadlineWarningGetter {
    use \Psr\Log\LoggerAwareTrait;

    public $entityManager;
    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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

    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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

class FakeSendDailyNotificationsCommandMonthlyPreviewGetter {
    use \Psr\Log\LoggerAwareTrait;

    public $entityManager;
    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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

    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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

class FakeSendDailyNotificationsCommandWeeklyPreviewGetter {
    use \Psr\Log\LoggerAwareTrait;

    public $entityManager;
    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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

class FakeSendDailyNotificationsCommandWeeklySummaryGetter {
    use \Psr\Log\LoggerAwareTrait;

    public $entityManager;
    public $dateUtils;
    public $envUtils;
    public $calledWithArgs;

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
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand
 */
final class SendDailyNotificationsCommandTest extends UnitTestCase {
    public function testSendDailyNotificationsCommand(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $notification_subscription_repo = new FakeSendDailyNotificationsCommandNotificationSubscriptionRepository();
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $telegram_link_repo = new FakeSendDailyNotificationsCommandTelegramLinkRepository();
        $entity_manager->repositories[TelegramLink::class] = $telegram_link_repo;
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $env_utils = new Fake\FakeEnvUtils();
        $email_utils = new Fake\FakeEmailUtils();
        $telegram_utils = new Fake\FakeTelegramUtils();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00');
        $logger = Fake\FakeLogger::create();
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
        $job->setDateUtils($date_utils);
        $job->setEmailUtils($email_utils);
        $job->setEntityManager($entity_manager);
        $job->setEnvUtils($env_utils);
        $job->setTelegramUtils($telegram_utils);
        $job->setLog($logger);
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
            "ERROR Error sending telegram to user (3): [Exception] provoked telegram error",
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
        ], $logger->handler->getPrettyRecords());

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
                'from' => ['fake@test.olzimmerberg.ch', 'OL Zimmerberg'],
                'replyTo' => null,
                'subject' => '[OLZ] MP title',
                'body' => 'MP text First',
                'altBody' => 'MP text First',
            ],
            [
                'user' => $user1,
                'from' => ['fake@test.olzimmerberg.ch', 'OL Zimmerberg'],
                'replyTo' => null,
                'subject' => '[OLZ] DW title {"days":3}',
                'body' => 'DW text First',
                'altBody' => 'DW text First',
            ],
            [
                'user' => $user1,
                'from' => ['fake@test.olzimmerberg.ch', 'OL Zimmerberg'],
                'replyTo' => null,
                'subject' => '[OLZ] DS title',
                'body' => 'DS text First',
                'altBody' => 'DS text First',
            ],
            [
                'user' => $user2,
                'from' => ['fake@test.olzimmerberg.ch', 'OL Zimmerberg'],
                'replyTo' => null,
                'subject' => '[OLZ] WS title',
                'body' => 'WS text Second',
                'altBody' => 'WS text Second',
            ],
            [
                'user' => $user1,
                'from' => ['fake@test.olzimmerberg.ch', 'OL Zimmerberg'],
                'replyTo' => null,
                'subject' => '[OLZ] ECR title {"cancelled":false}',
                'body' => 'ECR text First',
                'altBody' => 'ECR text First',
            ],
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
    }
}
