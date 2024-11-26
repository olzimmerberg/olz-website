<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\EmailConfigurationReminderGetter;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\FakeNotificationSubscription;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @coversNothing
 */
class EmailConfigurationReminderGetterForTest extends EmailConfigurationReminderGetter {
    /** @return array<int, array{reminder_id?: int, needs_reminder?: bool}> */
    public function testOnlyGetEmailConfigReminderState(): array {
        return $this->getEmailConfigReminderState();
    }

    /** @return array<string> */
    public function testOnlyGetNonReminderNotificationTypes(): array {
        return $this->getNonReminderNotificationTypes();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\EmailConfigurationReminderGetter
 */
final class EmailConfigurationReminderGetterTest extends UnitTestCase {
    public const NON_CONFIG_NOTIFICATION_TYPES = [
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        NotificationSubscription::TYPE_IMMEDIATE,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
    ];

    public function testEmailConfigurationReminderGetterGetEmailConfigReminderState(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $subscription_repo = $entity_manager->getRepository(NotificationSubscription::class);
        $subscription_repo->entityToBeFoundForQuery = fn ($query) => $this->subscriptionToBeFoundForQuery($query);
        $subscription_repo->entitiesToBeFoundForQuery = fn ($query) => $this->subscriptionsToBeFoundForQuery($query);
        $job = new EmailConfigurationReminderGetterForTest();

        $result = $job->testOnlyGetEmailConfigReminderState();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([
            1 => ['reminder_id' => 94857],
            2 => ['reminder_id' => 29475, 'needs_reminder' => true],
            3 => ['needs_reminder' => true],
        ], $result);
    }

    public function testEmailConfigurationReminderGetterAutogenerateSubscriptions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $subscription_repo = $entity_manager->getRepository(NotificationSubscription::class);
        $subscription_repo->entityToBeFoundForQuery = fn ($query) => $this->subscriptionToBeFoundForQuery($query);
        $subscription_repo->entitiesToBeFoundForQuery = fn ($query) => $this->subscriptionsToBeFoundForQuery($query);
        $job = new EmailConfigurationReminderGetter();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Removing email configuration reminder subscription (94857) for 'default (User ID: 1)'...",
            "INFO Generating email configuration reminder subscription for 'vorstand (User ID: 3)'...",
        ], $this->getLogs());
        $this->assertSame([
            [
                'vorstand (User ID: 3)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
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
            'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
            'notification_type' => $this::NON_CONFIG_NOTIFICATION_TYPES,
        ]) {
            return null;
        }
        if ($criteria === [
            'user' => FakeUser::adminUser(),
            'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
            'notification_type' => $this::NON_CONFIG_NOTIFICATION_TYPES,
        ]) {
            return null;
        }
        if ($criteria === [
            'user' => FakeUser::vorstandUser(),
            'delivery_type' => NotificationSubscription::DELIVERY_EMAIL,
            'notification_type' => $this::NON_CONFIG_NOTIFICATION_TYPES,
        ]) {
            return null;
        }

        if ($id === FakeNotificationSubscription::emailReminderDefault()->getId()) {
            return FakeNotificationSubscription::emailReminderDefault();
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
            && $notification_type === NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER
        ) {
            return [FakeNotificationSubscription::emailReminderDefault()];
        }

        if ($notification_type === NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER) {
            return [
                FakeNotificationSubscription::emailReminderDefault(),
                FakeNotificationSubscription::emailReminderAdmin(),
            ];
        }

        throw new \Exception("Not mocked");
    }

    // ---

    public function testEmailConfigurationReminderGetterOnWrongDay(): void {
        $not_the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH + 1;
        $not_the_day_str = str_pad("{$not_the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$not_the_day_str} 19:00:00");

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => false]);

        $this->assertNull($notification);
    }

    public function testEmailConfigurationReminderGetterCancelled(): void {
        $the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$the_day_str} 19:00:00");

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => true]);

        $this->assertNull($notification);
    }

    public function testEmailConfigurationReminderGetter(): void {
        $the_day = EmailConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-03-{$the_day_str} 19:00:00");
        $user = FakeUser::defaultUser();

        $job = new EmailConfigurationReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => false]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


            **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**

            In diesem Fall musst du dich auf der Website [*einloggen*](http://fake-base-url/_/apps/newsletter#login-dialog), und im ["Newsletter"-App](http://fake-base-url/_/apps/newsletter) (ist auch unter "Service" zu finden) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

            Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `default-user@staging.olzimmerberg.ch` registriert.


            **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

            Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


            ZZZZZZZZZZ;
        $this->assertSame('Kein Newsletter abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
