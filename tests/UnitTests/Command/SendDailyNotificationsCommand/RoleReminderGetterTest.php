<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\RoleReminderGetter;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Roles\Role;
use Olz\Tests\Fake\Entity\FakeNotificationSubscription;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

class TestOnlyRoleReminderGetter extends RoleReminderGetter {
    /** @return array<string, array{reminder_id?: int, needs_reminder?: bool}> */
    public function testOnlyGetRoleReminderState(): array {
        return $this->getRoleReminderState();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\RoleReminderGetter
 */
final class RoleReminderGetterTest extends UnitTestCase {
    public const NON_CONFIG_NOTIFICATION_TYPES = [
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        NotificationSubscription::TYPE_IMMEDIATE,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
    ];

    public function testRoleReminderGetterGetRoleReminderState(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $subscription_repo = $entity_manager->getRepository(NotificationSubscription::class);
        $subscription_repo->entityToBeFoundForQuery = fn ($query) => $this->subscriptionToBeFoundForQuery($query);
        $subscription_repo->entitiesToBeFoundForQuery = fn ($query) => $this->subscriptionsToBeFoundForQuery($query);
        $role_repo = $entity_manager->getRepository(Role::class);
        $role_repo->entitiesToBeFoundForQuery = fn ($query) => $this->rolesToBeFoundForQuery($query);
        $job = new TestOnlyRoleReminderGetter();

        $result = $job->testOnlyGetRoleReminderState();

        $this->assertSame([
            "WARNING Role reminder notification subscription (37586) without role ID",
        ], $this->getLogs());
        $this->assertSame([
            '1-1' => ['reminder_id' => 23859],
            '3-3' => ['reminder_id' => 92384, 'needs_reminder' => true],
            '2-' => ['reminder_id' => 37586],
            '2-2' => ['needs_reminder' => true],
            '2-1' => ['needs_reminder' => true],
            '3-1' => ['needs_reminder' => true],
        ], $result);
    }

    public function testRoleReminderGetterAutogenerateSubscriptions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $subscription_repo = $entity_manager->getRepository(NotificationSubscription::class);
        $subscription_repo->entityToBeFoundForQuery = fn ($query) => $this->subscriptionToBeFoundForQuery($query);
        $subscription_repo->entitiesToBeFoundForQuery = fn ($query) => $this->subscriptionsToBeFoundForQuery($query);
        $role_repo = $entity_manager->getRepository(Role::class);
        $role_repo->entitiesToBeFoundForQuery = fn ($query) => $this->rolesToBeFoundForQuery($query);
        $job = new RoleReminderGetter();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "WARNING Role reminder notification subscription (37586) without role ID",
            "INFO Removing role (1) reminder subscription (23859) for 'default (User ID: 1)'...",
            "INFO Removing role (0) reminder subscription (37586) for 'admin (User ID: 2)'...",
            "INFO Generating role (2) reminder subscription for 'admin (User ID: 2)'...",
            "INFO Generating role (1) reminder subscription for 'admin (User ID: 2)'...",
            "INFO Generating role (1) reminder subscription for 'vorstand (User ID: 3)'...",
        ], $this->getLogs());
        $this->assertSame([
            [
                'admin (User ID: 2)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_ROLE_REMINDER,
                '{"role_id":2,"cancelled":false}',
            ],
            [
                'admin (User ID: 2)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_ROLE_REMINDER,
                '{"role_id":1,"cancelled":false}',
            ],
            [
                'vorstand (User ID: 3)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_ROLE_REMINDER,
                '{"role_id":1,"cancelled":false}',
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
                NotificationSubscription::TYPE_ROLE_REMINDER,
                '{"role_id":1,"cancelled":false}',
            ],
            [
                'admin (User ID: 2)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_ROLE_REMINDER,
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
    protected function subscriptionToBeFoundForQuery(array $criteria): NotificationSubscription {
        if ($criteria === ['id' => FakeNotificationSubscription::roleReminderDefault()->getId()]) {
            return FakeNotificationSubscription::roleReminderDefault();
        }
        if ($criteria === ['id' => FakeNotificationSubscription::roleReminderBroken()->getId()]) {
            return FakeNotificationSubscription::roleReminderBroken();
        }

        throw new \Exception("Not mocked");
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<NotificationSubscription>
     */
    protected function subscriptionsToBeFoundForQuery(array $criteria): array {
        $notification_type = $criteria['notification_type'] ?? null;

        if ($notification_type === NotificationSubscription::TYPE_ROLE_REMINDER) {
            return [
                FakeNotificationSubscription::roleReminderDefault(),
                FakeNotificationSubscription::roleReminderVorstand(),
                FakeNotificationSubscription::roleReminderBroken(),
            ];
        }

        throw new \Exception("Not mocked");
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<Role>
     */
    protected function rolesToBeFoundForQuery(array $criteria): array {
        if ($criteria === ['on_off' => 1]) {
            return [
                FakeRole::adminRole(),
                FakeRole::vorstandRole(),
                FakeRole::someRole(),
            ];
        }

        throw new \Exception("Not mocked");
    }

    // ---

    public function testRoleReminderGetterOnWrongDay(): void {
        $the_day = substr(RoleReminderGetter::EXECUTION_DATE, 4, 6);
        $not_the_day = '-01-01';
        // @phpstan-ignore-next-line
        assert($the_day !== $not_the_day);
        $date_utils = new FixedDateUtils("2020{$not_the_day} 19:00:00");

        $job = new RoleReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['role' => 'default']);

        $this->assertNull($notification);
    }

    public function testRoleReminderGetter(): void {
        $the_day = substr(RoleReminderGetter::EXECUTION_DATE, 4, 6);
        $date_utils = new FixedDateUtils("2020{$the_day} 19:00:00");
        $user = FakeUser::defaultUser();

        $job = new RoleReminderGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['role_id' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Du bist im [OLZ-Organigramm](http://fake-base-url/_/verein) unter dem Ressort [**Vorstand ()**](http://fake-base-url/_/verein/vorstand_role) eingetragen, bzw. für dieses Ressort zuständig.

            **Vielen Dank, dass du mithilfst, unseren Verein am Laufen zu halten!**

            Um das Organigramm aktuell zu halten, bitten wir dich, die folgenden Punkte durchzugehen.

            **Falls etwas unklar ist, kontaktiere bitte den Website-Admin: website.fake@staging.olzimmerberg.ch!**

            - Bitte schau dir die [Präsenz deines Ressorts auf olzimmerberg.ch](http://fake-base-url/_/verein/vorstand_role) an, und **kontrolliere, ergänze und verbessere** gegebenenfalls die Angaben. Wenn du eingeloggt bist, kannst du diese direkt bearbeiten.
            - **Falls** du im kommenden Jahr nicht mehr für dieses Ressort zuständig sein kannst oder möchtest, bzw. nicht mehr unter diesem Ressort angezeigt werden solltest, kontaktiere bitte "deinen" Vorstand: Vorstand Mitglied, vorstand-user@staging.olzimmerberg.ch (oder den Präsi).
            - **Falls** du noch kein OLZ-Konto hast, erstelle doch eines ([zum Login-Dialog](http://fake-base-url/_/#login-dialog), dann "Noch kein OLZ-Konto?" wählen). Verwende den Benutzernamen "default", um automatisch Schreib-Zugriff für dein Ressort zu erhalten.

            Besten Dank für deine Mithilfe,
            
            Der Vorstand der OL Zimmerberg
            ZZZZZZZZZZ;
        $this->assertSame('Ressort-Erinnerung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
