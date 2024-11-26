<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command;

use Olz\Command\SendDailyNotificationsCommand;
use Olz\Command\SendDailyNotificationsCommand\Notification;
use Olz\Command\SendDailyNotificationsCommand\NotificationGetterInterface;
use Olz\Entity\NotificationSubscription;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @internal
 *
 * @coversNothing
 */
class SendDailyNotificationsCommandForTest extends SendDailyNotificationsCommand {
    /** @param array<string, NotificationGetterInterface> $new_notification_getters */
    public function testOnlySetNotificationGetters(array $new_notification_getters): void {
        $this->notification_getter_by_type = $new_notification_getters;
    }

    /** @return array<string, NotificationGetterInterface> $new_notification_getters */
    public function testOnlyGetNotificationGetters(): array {
        return $this->notification_getter_by_type;
    }
}

class FakeNotificationGetter implements NotificationGetterInterface {
    use WithUtilsTrait;

    protected string $notification_type;

    /** @var array<string, mixed> */
    public array $calledWithArgs;

    public function __construct(string $notification_type) {
        $this->notification_type = $notification_type;
    }

    public function autogenerateSubscriptions(): void {
        $this->log()->info("Autogenerating {$this->notification_type} subscriptions...");
    }

    /** @param array<string, mixed> $args */
    public function getNotification(array $args): ?Notification {
        $this->calledWithArgs = $args;
        if ($args['no_notification'] ?? false) {
            return null;
        }
        if ($args['cancelled'] ?? false) {
            return null;
        }
        if ($args['provoke_error'] ?? false) {
            return new Notification('provoke_error', 'provoke_error', [
                'notification_type' => $this->notification_type,
            ]);
        }
        $ident = implode('', array_map(function (string $part): string {
            return substr($part, 0, 1);
        }, explode('_', $this->notification_type)));
        $json_args = json_encode($args);
        return new Notification("{$ident} title {$json_args}", "{$ident} text %%userFirstName%%", [
            'notification_type' => $this->notification_type,
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

        $job = new SendDailyNotificationsCommandForTest();
        $job->setMailer($mailer);
        $job->testOnlySetNotificationGetters([
            NotificationSubscription::TYPE_DAILY_SUMMARY => new FakeNotificationGetter(NotificationSubscription::TYPE_DAILY_SUMMARY),
            NotificationSubscription::TYPE_DEADLINE_WARNING => new FakeNotificationGetter(NotificationSubscription::TYPE_DEADLINE_WARNING),
            NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER => new FakeNotificationGetter(NotificationSubscription::TYPE_EMAIL_CONFIG_REMINDER),
            NotificationSubscription::TYPE_MONTHLY_PREVIEW => new FakeNotificationGetter(NotificationSubscription::TYPE_MONTHLY_PREVIEW),
            NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER => new FakeNotificationGetter(NotificationSubscription::TYPE_TELEGRAM_CONFIG_REMINDER),
            NotificationSubscription::TYPE_WEEKLY_PREVIEW => new FakeNotificationGetter(NotificationSubscription::TYPE_WEEKLY_PREVIEW),
            NotificationSubscription::TYPE_WEEKLY_SUMMARY => new FakeNotificationGetter(NotificationSubscription::TYPE_WEEKLY_SUMMARY),
        ]);
        $job->run($input, $output);

        $this->assertSame([
            "INFO Running command Olz\\Tests\\UnitTests\\Command\\SendDailyNotificationsCommandForTest...",
            "INFO Autogenerating notification subscriptions...",
            "INFO Autogenerating daily_summary subscriptions...",
            "INFO Autogenerating deadline_warning subscriptions...",
            "INFO Autogenerating email_config_reminder subscriptions...",
            "INFO Autogenerating monthly_preview subscriptions...",
            "INFO Autogenerating telegram_config_reminder subscriptions...",
            "INFO Autogenerating weekly_preview subscriptions...",
            "INFO Autogenerating weekly_summary subscriptions...",
            "INFO Sending 'monthly_preview' notifications...",
            "INFO Getting notification for '[]'...",
            "INFO Sending notification mp title [] over email to user (1)...",
            "INFO Email sent to user (1): mp title []",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'weekly_preview' notifications...",
            "INFO Getting notification for '[]'...",
            "INFO Sending notification wp title [] over telegram to user (1)...",
            "INFO Telegram sent to user (1): wp title []",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'deadline_warning' notifications...",
            "INFO Getting notification for '{\"days\":7}'...",
            "INFO Sending notification dw title {\"days\":7} over telegram to user (1)...",
            "INFO Telegram sent to user (1): dw title {\"days\":7}",
            "INFO Getting notification for '{\"days\":3}'...",
            "INFO Sending notification dw title {\"days\":3} over telegram to user (2)...",
            "INFO Telegram sent to user (2): dw title {\"days\":3}",
            "INFO Sending notification dw title {\"days\":3} over telegram to user (3)...",
            "CRITICAL User (3) has a telegram link without chat ID, but a subscription (7)",
            "INFO Sending notification dw title {\"days\":3} over email to user (1)...",
            "INFO Email sent to user (1): dw title {\"days\":3}",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'daily_summary' notifications...",
            "INFO Getting notification for '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Sending notification ds title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true} over email to user (1)...",
            "INFO Email sent to user (1): ds title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'weekly_summary' notifications...",
            "INFO Getting notification for '{\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}'...",
            "INFO Sending notification ws title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true} over email to user (2)...",
            "INFO Email sent to user (2): ws title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true}",
            "INFO Sending notification ws title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true} over invalid-delivery to user (2)...",
            "CRITICAL Unknown delivery type 'invalid-delivery'",
            "INFO Sending notification ws title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true} over telegram to user (666)...",
            "NOTICE Error sending telegram to user (666): [Exception] provoked telegram error",
            "INFO Sending notification ws title {\"aktuell\":true,\"blog\":true,\"galerie\":true,\"forum\":true} over telegram to user (404)...",
            "NOTICE User (404) has no telegram link, but a subscription (22)",
            "INFO Getting notification for '{\"no_notification\":true}'...",
            "INFO Nothing to send.",
            "INFO Getting notification for '{\"provoke_error\":true}'...",
            "INFO Sending notification provoke_error over email to user (2)...",
            "CRITICAL Error sending email to user (2): [Exception] provoked",
            "INFO Sending 'invalid-type' notifications...",
            "CRITICAL Unknown notification type 'invalid-type'",
            "INFO Sending 'telegram_config_reminder' notifications...",
            "INFO Getting notification for '{\"cancelled\":false}'...",
            "INFO Sending notification tcr title {\"cancelled\":false} over telegram to user (2)...",
            "INFO Telegram sent to user (2): tcr title {\"cancelled\":false}",
            "INFO Getting notification for '{\"cancelled\":true}'...",
            "INFO Nothing to send.",
            "INFO Sending 'email_config_reminder' notifications...",
            "INFO Getting notification for '{\"cancelled\":false}'...",
            "INFO Sending notification ecr title {\"cancelled\":false} over email to user (1)...",
            "INFO Email sent to user (1): ecr title {\"cancelled\":false}",
            "INFO Getting notification for '{\"cancelled\":true}'...",
            "INFO Nothing to send.",
            "INFO Successfully ran command Olz\\Tests\\UnitTests\\Command\\SendDailyNotificationsCommandForTest.",
        ], $this->getLogs());

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->persisted);
        $this->assertSame([], $entity_manager->removed);

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] mp title []

                mp text Default

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                mp text Default
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] dw title {"days":3}

                dw text Default

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGVhZGxpbmVfd2FybmluZyJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                dw text Default
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGVhZGxpbmVfd2FybmluZyJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] ds title {"aktuell":true,"blog":true,"galerie":true,"forum":true}

                ds text Default

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGFpbHlfc3VtbWFyeSJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                ds text Default
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGFpbHlfc3VtbWFyeSJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Admin Istrator" <admin-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] ws title {"aktuell":true,"blog":true,"galerie":true,"forum":true}

                ws text Admin

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoid2Vla2x5X3N1bW1hcnkifQ
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                ws text Admin
                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoid2Vla2x5X3N1bW1hcnkifQ">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] ecr title {"cancelled":false}

                ecr text Default

                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZW1haWxfY29uZmlnX3JlbWluZGVyIn0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                ecr text Default
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
                'chat_id' => '99999',
                'parse_mode' => 'HTML',
                'text' => "<b>wp title []</b>\n\nwp text Default",
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '99999',
                'parse_mode' => 'HTML',
                'text' => "<b>dw title {\"days\":7}</b>\n\ndw text Default",
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '88888',
                'parse_mode' => 'HTML',
                'text' => "<b>dw title {\"days\":3}</b>\n\ndw text Admin",
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '88888',
                'parse_mode' => 'HTML',
                'text' => "<b>tcr title {\"cancelled\":false}</b>\n\ntcr text Admin",
                'disable_web_page_preview' => true,
            ]],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
        $notification_getters = $job->testOnlyGetNotificationGetters();

        foreach ($notification_getters as $type => $notification_getter) {
            // @phpstan-ignore-next-line
            $this->assertSame($entity_manager, $notification_getter->entityManager(), "{$type}");
        }
    }
}
