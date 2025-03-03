<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\FakeNotificationSubscription;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

class TestOnlyTelegramConfigurationReminderGetter extends TelegramConfigurationReminderGetter {
    /** @return array<int, array{reminder_id?: int, needs_reminder?: bool}> */
    public function testOnlyGetTelegramConfigReminderState(): array {
        return $this->getTelegramConfigReminderState();
    }

    /** @return array<string> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter
 */
final class TelegramConfigurationReminderGetterTest extends UnitTestCase {
    public const NON_CONFIG_NOTIFICATION_TYPES = [
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        NotificationSubscription::TYPE_IMMEDIATE,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
    ];

    public function testTelegramConfigurationReminderGetterGetTelegramConfigReminderState(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $subscription_repo = $entity_manager->getRepository(NotificationSubscription::class);
        $subscription_repo->entityToBeFoundForQuery = fn ($query) => $this->subscriptionToBeFoundForQuery($query);
        $subscription_repo->entitiesToBeFoundForQuery = fn ($query) => $this->subscriptionsToBeFoundForQuery($query);
        $job = new TestOnlyTelegramConfigurationReminderGetter();

        $result = $job->testOnlyGetTelegramConfigReminderState();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([
            1 => ['reminder_id' => 93865, 'needs_reminder' => true],
            2 => ['reminder_id' => 10246],
            3 => ['needs_reminder' => true],
        ], $result);
    }

    public function testTelegramConfigurationReminderGetterAutogenerateSubscriptions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $subscription_repo = $entity_manager->getRepository(NotificationSubscription::class);
        $subscription_repo->entityToBeFoundForQuery = fn ($query) => $this->subscriptionToBeFoundForQuery($query);
        $subscription_repo->entitiesToBeFoundForQuery = fn ($query) => $this->subscriptionsToBeFoundForQuery($query);
        $job = new TelegramConfigurationReminderGetter();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Removing telegram configuration reminder subscription (10246) for 'admin (User ID: 2)'...",
            "INFO Generating telegram configuration reminder subscription for 'vorstand (User ID: 3)'...",
        ], $this->getLogs());
        $this->assertSame([
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
                'admin (User ID: 2)',
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
    }

    /** @param array<string, mixed> $criteria */
    protected function subscriptionToBeFoundForQuery(array $criteria): ?NotificationSubscription {
        $id = $criteria['id'] ?? null;

        if ($criteria === [
            'user' => FakeUser::defaultUser(),
            'delivery_type' => NotificationSubscription::DELIVERY_TELEGRAM,
            'notification_type' => $this::NON_CONFIG_NOTIFICATION_TYPES,
        ]) {
            return null;
        }
        if ($criteria === [
            'user' => FakeUser::vorstandUser(),
            'delivery_type' => NotificationSubscription::DELIVERY_TELEGRAM,
            'notification_type' => $this::NON_CONFIG_NOTIFICATION_TYPES,
        ]) {
            return null;
        }

        if ($id === FakeNotificationSubscription::telegramReminderAdmin()->getId()) {
            return FakeNotificationSubscription::telegramReminderAdmin();
        }

        throw new \Exception("Not mocked");
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<NotificationSubscription>
     */
    protected function subscriptionsToBeFoundForQuery(array $criteria): array {
        $user = $criteria['user'] ?? null;
        if ($user instanceof User) {
            $user = $user->getId();
        }
        $notification_type = $criteria['notification_type'] ?? null;

        if (
            $user === FakeUser::defaultUser()->getId()
            && $notification_type === NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER
        ) {
            return [
                FakeNotificationSubscription::telegramReminderDefault(),
            ];
        }

        if ($notification_type === NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER) {
            return [
                FakeNotificationSubscription::telegramReminderDefault(),
                FakeNotificationSubscription::telegramReminderAdmin(),
            ];
        }

        throw new \Exception("Not mocked");
    }

    // ---

    public function testTelegramConfigurationReminderGetterOnWrongDay(): void {
        $not_the_day = TelegramConfigurationReminderGetter::DAY_OF_MONTH + 1;
        $not_the_day_str = str_pad("{$not_the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$not_the_day_str} 19:30:00");

        $job = new TelegramConfigurationReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => false]);

        $this->assertNull($notification);
    }

    public function testTelegramConfigurationReminderGetterCancelled(): void {
        $the_day = TelegramConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$the_day_str} 19:00:00");

        $job = new TelegramConfigurationReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => true]);

        $this->assertNull($notification);
    }

    public function testTelegramConfigurationReminderGetter(): void {
        $the_day = TelegramConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$the_day_str} 19:00:00");
        $user = FakeUser::defaultUser();

        $job = new TelegramConfigurationReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => false]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.


            **Du möchtest eigentlich Push-Nachrichten erhalten?**

            In diesem Fall musst du dich auf der Website *einloggen*, und im ["Newsletter"-App](http://fake-base-url/_/apps/newsletter) (ist auch unter "Service" zu finden) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


            **Du möchtest gar keine Push-Nachrichten erhalten?**

            Dann lösche einfach diesen Chat.


            ZZZZZZZZZZ;
        $this->assertSame('Keine Push-Nachrichten abonniert', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
