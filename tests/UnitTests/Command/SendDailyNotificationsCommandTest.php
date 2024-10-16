<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SendDailyNotificationsCommand;
use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Command\SendDailyNotificationsCommand\EmailConfigurationReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter;
use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter;
use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\TelegramLink;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeNotificationSubscription;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

$user1 = FakeUser::defaultUser(true);
$user1->setId(1);
$user1->setFirstName('First');
$user1->setLastName('User');

$user2 = FakeUser::defaultUser(true);
$user2->setId(2);
$user2->setFirstName('Second');
$user2->setLastName('User');

$user3 = FakeUser::defaultUser(true);
$user3->setId(3);
$user3->setFirstName('Third');
$user3->setLastName('User');

$user_provoke_error = FakeUser::defaultUser(true);
$user_provoke_error->setId(3);
$user_provoke_error->setFirstName('Provoke');
$user_provoke_error->setLastName('Error');

$user_no_telegram_link = FakeUser::defaultUser(true);
$user_no_telegram_link->setId(4);
$user_no_telegram_link->setFirstName('No Telegram');
$user_no_telegram_link->setLastName('Link');

$subscription_1 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_1->setId(1);
$subscription_1->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_1->setUser($user1);
$subscription_1->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
$subscription_1->setNotificationTypeArgs(json_encode([]));
$subscription_2 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_2->setId(2);
$subscription_2->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_2->setUser($user2);
$subscription_2->setNotificationType(NotificationSubscription::TYPE_MONTHLY_PREVIEW);
$subscription_2->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_3 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_3->setId(3);
$subscription_3->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_3->setUser($user1);
$subscription_3->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
$subscription_3->setNotificationTypeArgs(json_encode([]));
$subscription_4 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_4->setId(4);
$subscription_4->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_4->setUser($user1);
$subscription_4->setNotificationType(NotificationSubscription::TYPE_WEEKLY_PREVIEW);
$subscription_4->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_5 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_5->setId(5);
$subscription_5->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_5->setUser($user1);
$subscription_5->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_5->setNotificationTypeArgs(json_encode(['days' => 7]));
$subscription_6 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_6->setId(6);
$subscription_6->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_6->setUser($user2);
$subscription_6->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_6->setNotificationTypeArgs(json_encode(['days' => 3]));
$subscription_7 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_7->setId(7);
$subscription_7->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_7->setUser($user3);
$subscription_7->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_7->setNotificationTypeArgs(json_encode(['days' => 3]));
$subscription_8 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_8->setId(8);
$subscription_8->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_8->setUser($user1);
$subscription_8->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_8->setNotificationTypeArgs(json_encode(['days' => 3]));
$subscription_9 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_9->setId(9);
$subscription_9->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_9->setUser($user1);
$subscription_9->setNotificationType(NotificationSubscription::TYPE_DEADLINE_WARNING);
$subscription_9->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_10 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_10->setId(10);
$subscription_10->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_10->setUser($user1);
$subscription_10->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
$subscription_10->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_11 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_11->setId(11);
$subscription_11->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_11->setUser($user1);
$subscription_11->setNotificationType(NotificationSubscription::TYPE_DAILY_SUMMARY);
$subscription_11->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_12 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_12->setId(12);
$subscription_12->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_12->setUser($user2);
$subscription_12->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_12->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_13 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_13->setId(13);
$subscription_13->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_13->setUser($user2);
$subscription_13->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_13->setNotificationTypeArgs(json_encode(['no_notification' => true]));
$subscription_14 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_14->setId(14);
$subscription_14->setDeliveryType('invalid-delivery');
$subscription_14->setUser($user2);
$subscription_14->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_14->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_15 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_15->setId(15);
$subscription_15->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_15->setUser($user2);
$subscription_15->setNotificationType('invalid-type');
$subscription_15->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_16 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_16->setId(16);
$subscription_16->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_16->setUser($user2);
$subscription_16->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_16->setNotificationTypeArgs(json_encode(['provoke_error' => true]));
$subscription_17 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_17->setId(17);
$subscription_17->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_17->setUser($user_provoke_error);
$subscription_17->setNotificationType(NotificationSubscription::TYPE_WEEKLY_SUMMARY);
$subscription_17->setNotificationTypeArgs(json_encode(['aktuell' => true, 'blog' => true, 'galerie' => true, 'forum' => true]));
$subscription_18 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_18->setId(18);
$subscription_18->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_18->setUser($user2);
$subscription_18->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
$subscription_18->setNotificationTypeArgs(json_encode(['cancelled' => false]));
$subscription_19 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_19->setId(19);
$subscription_19->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
$subscription_19->setUser($user2);
$subscription_19->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
$subscription_19->setNotificationTypeArgs(json_encode(['cancelled' => true]));
$subscription_20 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_20->setId(20);
$subscription_20->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_20->setUser($user1);
$subscription_20->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
$subscription_20->setNotificationTypeArgs(json_encode(['cancelled' => false]));
$subscription_21 = FakeNotificationSubscription::defaultNotificationSubscription(true);
$subscription_21->setId(21);
$subscription_21->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
$subscription_21->setUser($user1);
$subscription_21->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
$subscription_21->setNotificationTypeArgs(json_encode(['cancelled' => true]));
$subscription_22 = FakeNotificationSubscription::defaultNotificationSubscription(true);
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

/**
 * @extends FakeOlzRepository<NotificationSubscription>
 */
class FakeSendDailyNotificationsCommandNotificationSubscriptionRepository extends FakeOlzRepository {
    /** @return array<NotificationSubscription> */
    public function findAll(): array {
        global $all_notification_subscriptions;
        return $all_notification_subscriptions;
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $orderBy
     * @param mixed|null           $limit
     * @param mixed|null           $offset
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        global $user1, $user2, $user3, $user_provoke_error, $all_notification_subscriptions;

        if ($criteria === ['notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER]) {
            $subscription_31 = FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_31->setId(1);
            $subscription_31->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
            $subscription_31->setUser($user1);
            $subscription_31->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
            $subscription_31->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            $subscription_32 = FakeNotificationSubscription::defaultNotificationSubscription(true);
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

        if ($criteria === [
            'user' => FakeUser::defaultUser(),
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]) {
            $subscription_41 = FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_41->setId(1);
            $subscription_41->setDeliveryType(NotificationSubscription::DELIVERY_EMAIL);
            $subscription_41->setUser($user1);
            $subscription_41->setNotificationType(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER);
            $subscription_41->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            return [
                $subscription_41,
            ];
        }

        if ($criteria === ['notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER]) {
            $subscription_51 = FakeNotificationSubscription::defaultNotificationSubscription(true);
            $subscription_51->setId(1);
            $subscription_51->setDeliveryType(NotificationSubscription::DELIVERY_TELEGRAM);
            $subscription_51->setUser($user1);
            $subscription_51->setNotificationType(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER);
            $subscription_51->setNotificationTypeArgs(json_encode(['cancelled' => false]));
            $subscription_52 = FakeNotificationSubscription::defaultNotificationSubscription(true);
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

        if ($criteria === [
            'user' => FakeUser::defaultUser(),
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]) {
            $subscription_61 = FakeNotificationSubscription::defaultNotificationSubscription(true);
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

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        global $user1;
        if ($criteria['user'] === $user1) {
            return new NotificationSubscription();
        }
        return null;
    }
}

/**
 * @extends FakeOlzRepository<TelegramLink>
 */
class FakeSendDailyNotificationsCommandTelegramLinkRepository extends FakeOlzRepository {
    /** @param array<string, mixed> $criteria */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        global $user1, $user2, $user3, $user_provoke_error;
        if ($criteria == ['user' => $user1]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('11111');
            return $telegram_link;
        }
        if ($criteria == ['user' => $user2]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('22222');
            return $telegram_link;
        }
        if ($criteria == ['user' => $user3]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId(null);
            return $telegram_link;
        }
        if ($criteria == ['user' => $user_provoke_error]) {
            $telegram_link = new TelegramLink();
            $telegram_link->setTelegramChatId('provoke_error');
            return $telegram_link;
        }
        return null;
    }

    /** @return array<TelegramLink> */
    public function getActivatedTelegramLinks(): array {
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

class FakeSendDailyNotificationsCommandDailySummaryGetter extends DailySummaryGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getDailySummaryNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('DS title', 'DS text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_DAILY_SUMMARY,
        ]);
    }
}

class FakeSendDailyNotificationsCommandDeadlineWarningGetter extends DeadlineWarningGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getDeadlineWarningNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("DW title {$args_str}", 'DW text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_DEADLINE_WARNING,
        ]);
    }
}

class FakeSendDailyNotificationsEmailConfigurationReminderGetter extends EmailConfigurationReminderGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("ECR title {$args_str}", 'ECR text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
        ]);
    }
}

class FakeSendDailyNotificationsCommandMonthlyPreviewGetter extends MonthlyPreviewGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getMonthlyPreviewNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('MP title', 'MP text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        ]);
    }
}

class FakeSendDailyNotificationsTelegramConfigurationReminderGetter extends TelegramConfigurationReminderGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['cancelled'] ?? false) {
            return null;
        }
        $args_str = json_encode($args);
        return new Notification("TCR title {$args_str}", 'TCR text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER,
        ]);
    }
}

class FakeSendDailyNotificationsCommandWeeklyPreviewGetter extends WeeklyPreviewGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getWeeklyPreviewNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        return new Notification('WP title', 'WP text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        ]);
    }
}

class FakeSendDailyNotificationsCommandWeeklySummaryGetter extends WeeklySummaryGetter {
    use WithUtilsTrait;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    /** @param array<string, mixed> $args */
    public function getWeeklySummaryNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        if ($args['provoke_error'] ?? false) {
            return new Notification('provoke_error', 'provoke_error', [
                'notification_type' => NotificationSubscription::TYPE_WEEKLY_SUMMARY,
            ]);
        }
        return new Notification('WS title', 'WS text %%userFirstName%%', [
            'notification_type' => NotificationSubscription::TYPE_WEEKLY_SUMMARY,
        ]);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand
 */
final class SendDailyNotificationsCommandTest extends UnitTestCase {
    public function testSendDailyNotificationsCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        $entity_manager = WithUtilsCache::get('entityManager');
        $notification_subscription_repo = new FakeSendDailyNotificationsCommandNotificationSubscriptionRepository($entity_manager);
        $entity_manager->repositories[NotificationSubscription::class] = $notification_subscription_repo;
        $telegram_link_repo = new FakeSendDailyNotificationsCommandTelegramLinkRepository($entity_manager);
        $entity_manager->repositories[TelegramLink::class] = $telegram_link_repo;
        $daily_summary_getter = new FakeSendDailyNotificationsCommandDailySummaryGetter();
        $deadline_warning_getter = new FakeSendDailyNotificationsCommandDeadlineWarningGetter();
        $email_configuration_reminder_getter = new FakeSendDailyNotificationsEmailConfigurationReminderGetter();
        $monthly_preview_getter = new FakeSendDailyNotificationsCommandMonthlyPreviewGetter();
        $telegram_configuration_reminder_getter = new FakeSendDailyNotificationsTelegramConfigurationReminderGetter();
        $weekly_preview_getter = new FakeSendDailyNotificationsCommandWeeklyPreviewGetter();
        $weekly_summary_getter = new FakeSendDailyNotificationsCommandWeeklySummaryGetter();
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(6))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                if (str_contains($email->getSubject(), 'provoke')) {
                    throw new \Exception("provoked");
                }
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $job = new SendDailyNotificationsCommand();
        $job->setMailer($mailer);
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
            "INFO Removing email configuration reminder subscription for 'default (User ID: 1)'...",
            "INFO Generating email configuration reminder subscription for 'vorstand (User ID: 3)'...",
            "INFO Removing telegram configuration reminder subscription for 'default (User ID: 1)'...",
            "INFO Generating telegram configuration reminder subscription for 'vorstand (User ID: 3)'...",
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
            "CRITICAL Error sending email to user (2): [Exception] provoked",
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
                'vorstand (User ID: 3)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
            [
                'vorstand (User ID: 3)',
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
                'default (User ID: 1)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
            [
                'default (User ID: 1)',
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
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "First User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] MP title

                MP text First

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                MP text First
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "First User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] DW title {"days":3}

                DW text First

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGVhZGxpbmVfd2FybmluZyJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                DW text First
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGVhZGxpbmVfd2FybmluZyJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "First User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] DS title

                DS text First

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGFpbHlfc3VtbWFyeSJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                DS text First
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGFpbHlfc3VtbWFyeSJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Second User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] WS title

                WS text Second

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoid2Vla2x5X3N1bW1hcnkifQ
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                WS text Second
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoid2Vla2x5X3N1bW1hcnkifQ">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "First User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] ECR title {"cancelled":false}

                ECR text First

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZW1haWxfY29uZmlnX3JlbWluZGVyIn0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                ECR text First
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZW1haWxfY29uZmlnX3JlbWluZGVyIn0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

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
