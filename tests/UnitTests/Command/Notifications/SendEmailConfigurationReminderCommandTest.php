<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendEmailConfigurationReminderCommand;
use Olz\Entity\NotificationSubscription;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestOnlySendEmailConfigurationReminderCommand extends SendEmailConfigurationReminderCommand {
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
 * @covers \Olz\Command\Notifications\SendEmailConfigurationReminderCommand
 */
final class SendEmailConfigurationReminderCommandTest extends UnitTestCase {
    public const NON_CONFIG_NOTIFICATION_TYPES = [
        NotificationSubscription::TYPE_DAILY_SUMMARY,
        NotificationSubscription::TYPE_DEADLINE_WARNING,
        NotificationSubscription::TYPE_IMMEDIATE,
        NotificationSubscription::TYPE_MONTHLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_PREVIEW,
        NotificationSubscription::TYPE_WEEKLY_SUMMARY,
    ];

    public function testSendEmailConfigurationReminderCommand(): void {
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

        $job = new SendEmailConfigurationReminderCommand();
        $the_day = SendEmailConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $job->setDateUtils(new DateUtils("2020-03-{$the_day_str} 19:00:00"));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendEmailConfigurationReminderCommand...',
            'INFO Generating email configuration reminder subscriptions...',
            'INFO Removing email configuration reminder subscription (21) for \'default (User ID: 1)\'...',
            'INFO Sending \'email_config_reminder\' notifications...',
            'INFO Getting notification for \'{"cancelled":false}\'...',
            'INFO Sending notification Kein Newsletter abonniert over email to user (1)...',
            'DEBUG Sending email to "Default User" <default-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (1): Kein Newsletter abonniert',
            'INFO Getting notification for \'{"cancelled":true}\'...',
            'INFO Nothing to send.',
            'INFO Successfully ran command Olz\Command\Notifications\SendEmailConfigurationReminderCommand.',
        ], $this->getLogs());

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Kein Newsletter abonniert

                Hallo Default,

                Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


                **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**

                In diesem Fall musst du dich auf der Website [*einloggen*](http://fake-base-url/_/apps/newsletter#login-dialog), und im ["Newsletter"-App](http://fake-base-url/_/apps/newsletter) (ist auch unter "Service" zu finden) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

                Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `default-user@staging.olzimmerberg.ch` registriert.


                **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

                Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".



                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZW1haWxfY29uZmlnX3JlbWluZGVyIn0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Default,

                Leider hast du bisher keinerlei OLZ-Newsletter-Benachrichtigungen abonniert.


                **Du möchtest eigentlich OLZ-Newsletter-Benachrichtigungen erhalten?**

                In diesem Fall musst du dich auf der Website [*einloggen*](http://fake-base-url/_/apps/newsletter#login-dialog), und im ["Newsletter"-App](http://fake-base-url/_/apps/newsletter) (ist auch unter "Service" zu finden) bei "E-Mail Newsletter" die gewünschten Benachrichtigungen auswählen.

                Falls du dein Passwort vergessen hast, kannst du es im Login-Dialog bei "Passwort vergessen?" zurücksetzen. Du bist mit der E-Mail Adresse `default-user@staging.olzimmerberg.ch` registriert.


                **Du möchtest auch weiterhin keine OLZ-Newsletter-Benachrichtigungen erhalten?**

                Dann ignoriere dieses E-Mail. Wenn du es nicht deaktivierst, wird dir dieses E-Mail nächsten Monat allerdings erneut zugesendet. Um dich abzumelden, klicke unten auf "Keine solchen E-Mails mehr".


                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZW1haWxfY29uZmlnX3JlbWluZGVyIn0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendEmailConfigurationReminderCommandGetEmailConfigReminderState(): void {
        WithUtilsCache::set('dateUtils', new DateUtils('2006-01-13 18:43:36'));
        $job = new TestOnlySendEmailConfigurationReminderCommand();

        $result = $job->testOnlyGetEmailConfigReminderState();

        $this->assertSame([], $this->getLogs());
        $this->assertSame([
            1 => ['reminder_id' => 21],
            2 => ['needs_reminder' => true],
            3 => ['needs_reminder' => true],
        ], $result);
    }

    public function testSendEmailConfigurationReminderCommandAutogenerateSubscriptions(): void {
        WithUtilsCache::set('dateUtils', new DateUtils('2006-01-13 18:43:36'));
        $entity_manager = WithUtilsCache::get('entityManager');
        $job = new SendEmailConfigurationReminderCommand();

        $job->autogenerateSubscriptions();

        $this->assertSame([
            "INFO Generating email configuration reminder subscriptions...",
            'INFO Removing email configuration reminder subscription (21) for \'default (User ID: 1)\'...',
            'INFO Generating email configuration reminder subscription for \'admin (User ID: 2)\'...',
            'INFO Generating email configuration reminder subscription for \'vorstand (User ID: 3)\'...',
        ], $this->getLogs());
        $this->assertSame([
            [
                'admin (User ID: 2)',
                NotificationSubscription::DELIVERY_EMAIL,
                NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER,
                '{"cancelled":false}',
            ],
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
                '{"cancelled":true}',
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

    public function testSendEmailConfigurationReminderCommandOnWrongDay(): void {
        $not_the_day = SendEmailConfigurationReminderCommand::DAY_OF_MONTH + 1;
        $not_the_day_str = str_pad("{$not_the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-03-{$not_the_day_str} 19:00:00");

        $job = new SendEmailConfigurationReminderCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => false]);

        $this->assertNull($notification);
    }

    public function testSendEmailConfigurationReminderCommandCancelled(): void {
        $the_day = SendEmailConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-03-{$the_day_str} 19:00:00");

        $job = new SendEmailConfigurationReminderCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['cancelled' => true]);

        $this->assertNull($notification);
    }

    public function testSendEmailConfigurationReminderCommandNotification(): void {
        $the_day = SendEmailConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-03-{$the_day_str} 19:00:00");
        $user = FakeUser::defaultUser();

        $job = new SendEmailConfigurationReminderCommand();
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
        $this->assertSame('Kein Newsletter abonniert', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
