<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendRoleReminderCommand;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Roles\Role;
use Olz\Tests\Fake\Entity\Roles\FakeRole;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestOnlySendRoleReminderCommand extends SendRoleReminderCommand {
    /** @return array<string, array{reminder_id?: int, needs_reminder?: bool}> */
    public function testOnlyGetRoleReminderState(): array {
        return $this->getRoleReminderState();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendRoleReminderCommand
 */
final class SendRoleReminderCommandTest extends UnitTestCase {
    public const NON_CONFIG_NOTIFICATION_TYPES = [
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        NotificationSubscription::TYPE_IMMEDIATE,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
    ];

    public function testSendRoleReminderCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                if (str_contains($email->getSubject() ?? '', 'provoke')) {
                    throw new \Exception("provoked");
                }
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $job = new SendRoleReminderCommand();
        $the_day = substr(SendRoleReminderCommand::EXECUTION_DATE, 4, 6);
        $job->setDateUtils(new DateUtils("2020{$the_day} 19:00:00"));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendRoleReminderCommand...',
            'INFO Generating role reminder subscriptions...',
            'INFO Removing role (1) reminder subscription (23) for \'default (User ID: 1)\'...',
            'INFO Generating role (1234) reminder subscription for \'maximal-user (User ID: 1234)\'...',
            'INFO Generating role (1234) reminder subscription for \'empty-user (User ID: 123)\'...',
            'INFO Generating role (1234) reminder subscription for \'minimal-user (User ID: 12)\'...',
            'INFO Sending \'role_reminder\' notifications...',
            'INFO Getting notification for \'{"role_id":1,"cancelled":false}\'...',
            'INFO Sending notification Ressort-Erinnerung over email to user (1)...',
            'DEBUG Sending email to "Default User" <default-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (1): Ressort-Erinnerung',
            'INFO Successfully ran command Olz\Command\Notifications\SendRoleReminderCommand.',
        ], $this->getLogs());

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Ressort-Erinnerung

                Hallo Default,

                Du bist im [OLZ-Organigramm](http://fake-base-url/_/verein) unter dem Ressort [**Default**](http://fake-base-url/_/verein/role) eingetragen, bzw. für dieses Ressort zuständig.

                **Vielen Dank, dass du mithilfst, unseren Verein am Laufen zu halten!**

                Um das Organigramm aktuell zu halten, bitten wir dich, die folgenden Punkte durchzugehen.

                **Falls etwas unklar ist, kontaktiere bitte den Website-Admin: website.fake@staging.olzimmerberg.ch!**

                - Bitte schau dir die [Präsenz deines Ressorts auf olzimmerberg.ch](http://fake-base-url/_/verein/role) an, und **kontrolliere, ergänze und verbessere** gegebenenfalls die Angaben. Wenn du eingeloggt bist, kannst du diese direkt bearbeiten.
                - **Falls** du im kommenden Jahr nicht mehr für dieses Ressort zuständig sein kannst oder möchtest, bzw. nicht mehr unter diesem Ressort angezeigt werden solltest, kontaktiere bitte "deinen" Vorstand:  (oder den Präsi).
                - **Falls** du noch kein OLZ-Konto hast, erstelle doch eines ([zum Login-Dialog](http://fake-base-url/_/#login-dialog), dann "Noch kein OLZ-Konto?" wählen). Verwende den Benutzernamen "default", um automatisch Schreib-Zugriff für dein Ressort zu erhalten.

                Besten Dank für deine Mithilfe,

                Der Vorstand der OL Zimmerberg

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoicm9sZV9yZW1pbmRlciJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Default,

                Du bist im [OLZ-Organigramm](http://fake-base-url/_/verein) unter dem Ressort [**Default**](http://fake-base-url/_/verein/role) eingetragen, bzw. für dieses Ressort zuständig.

                **Vielen Dank, dass du mithilfst, unseren Verein am Laufen zu halten!**

                Um das Organigramm aktuell zu halten, bitten wir dich, die folgenden Punkte durchzugehen.

                **Falls etwas unklar ist, kontaktiere bitte den Website-Admin: website.fake@staging.olzimmerberg.ch!**

                - Bitte schau dir die [Präsenz deines Ressorts auf olzimmerberg.ch](http://fake-base-url/_/verein/role) an, und **kontrolliere, ergänze und verbessere** gegebenenfalls die Angaben. Wenn du eingeloggt bist, kannst du diese direkt bearbeiten.
                - **Falls** du im kommenden Jahr nicht mehr für dieses Ressort zuständig sein kannst oder möchtest, bzw. nicht mehr unter diesem Ressort angezeigt werden solltest, kontaktiere bitte "deinen" Vorstand:  (oder den Präsi).
                - **Falls** du noch kein OLZ-Konto hast, erstelle doch eines ([zum Login-Dialog](http://fake-base-url/_/#login-dialog), dann "Noch kein OLZ-Konto?" wählen). Verwende den Benutzernamen "default", um automatisch Schreib-Zugriff für dein Ressort zu erhalten.

                Besten Dank für deine Mithilfe,

                Der Vorstand der OL Zimmerberg
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoicm9sZV9yZW1pbmRlciJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendRoleReminderCommandGetRoleReminderState(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $role_repo = $entity_manager->getRepository(Role::class);
        $role_repo->entitiesToBeFoundForQuery = fn ($query) => $this->rolesToBeFoundForQuery($query);
        $job = new TestOnlySendRoleReminderCommand();

        $result = $job->testOnlyGetRoleReminderState();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([
            '1-1' => ['reminder_id' => 23],
            '2-2' => ['needs_reminder' => true],
            '3-3' => ['needs_reminder' => true],
            '2-1' => ['needs_reminder' => true],
            '3-1' => ['needs_reminder' => true],
        ], $result);
    }

    public function testSendRoleReminderCommandAutogenerateSubscriptions(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $role_repo = $entity_manager->getRepository(Role::class);
        $role_repo->entitiesToBeFoundForQuery = fn ($query) => $this->rolesToBeFoundForQuery($query);
        $job = new SendRoleReminderCommand();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Generating role reminder subscriptions...",
            'INFO Removing role (1) reminder subscription (23) for \'default (User ID: 1)\'...',
            'INFO Generating role (2) reminder subscription for \'admin (User ID: 2)\'...',
            'INFO Generating role (3) reminder subscription for \'vorstand (User ID: 3)\'...',
            'INFO Generating role (1) reminder subscription for \'admin (User ID: 2)\'...',
            'INFO Generating role (1) reminder subscription for \'vorstand (User ID: 3)\'...',
        ], $this->getLogs());
        $this->assertSame([
            [
                'admin (User ID: 2)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_ROLE_REMINDER,
                '{"role_id":2,"cancelled":false}',
            ],
            [
                'vorstand (User ID: 3)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_ROLE_REMINDER,
                '{"role_id":3,"cancelled":false}',
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

    public function testSendRoleReminderCommandOnWrongDay(): void {
        $the_day = substr(SendRoleReminderCommand::EXECUTION_DATE, 4, 6);
        $not_the_day = '-01-01';
        // @phpstan-ignore-next-line
        assert($the_day !== $not_the_day);
        $date_utils = new DateUtils("2020{$not_the_day} 19:00:00");

        $job = new SendRoleReminderCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['role' => 'default']);

        $this->assertNull($notification);
    }

    public function testSendRoleReminderCommandNotification(): void {
        $the_day = substr(SendRoleReminderCommand::EXECUTION_DATE, 4, 6);
        $date_utils = new DateUtils("2020{$the_day} 19:00:00");
        $user = FakeUser::defaultUser();

        $job = new SendRoleReminderCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['role_id' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Du bist im [OLZ-Organigramm](http://fake-base-url/_/verein) unter dem Ressort [**Vorstand**](http://fake-base-url/_/verein/vorstand_role) eingetragen, bzw. für dieses Ressort zuständig.

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
