<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendDeadlineWarningCommand;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendDeadlineWarningCommand
 */
final class SendDeadlineWarningCommandTest extends UnitTestCase {
    public function testSendDeadlineWarningCommand(): void {
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

        $job = new SendDeadlineWarningCommand();
        $job->setDateUtils(new DateUtils('2020-03-10 16:00:00'));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendDeadlineWarningCommand...',
            'INFO Sending \'deadline_warning\' notifications...',
            'INFO Getting notification for \'{"days":7}\'...',
            'INFO Nothing to send.',
            'INFO Getting notification for \'{"days":3}\'...',
            'INFO Sending notification Meldeschlusswarnung over telegram to user (2)...',
            'INFO Telegram sent to user (2): Meldeschlusswarnung',
            'INFO Sending notification Meldeschlusswarnung over telegram to user (3)...',
            'CRITICAL User (3) has a telegram link without chat ID, but a subscription (7)',
            'INFO Sending notification Meldeschlusswarnung over email to user (1)...',
            'DEBUG Sending email to "Default User" <default-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (1): Meldeschlusswarnung',
            'INFO Getting notification for \'{"no_notification":true}\'...',
            'ERROR Error running command Olz\Command\Notifications\SendDeadlineWarningCommand: Undefined array key "days".',
        ], $this->getLogs());

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Meldeschlusswarnung

                Hallo Default,

                Folgende Meldeschlüsse stehen bevor:

                - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGVhZGxpbmVfd2FybmluZyJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Default,

                Folgende Meldeschlüsse stehen bevor:

                - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'

                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGVhZGxpbmVfd2FybmluZyJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

        $this->assertSame([
            ['sendMessage', [
                'chat_id' => '88888',
                'parse_mode' => 'HTML',
                'text' => <<<'ZZZZZZZZZZ'
                    <b>Meldeschlusswarnung</b>

                    Hallo Admin,

                    Folgende Meldeschlüsse stehen bevor:

                    - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'

                    ZZZZZZZZZZ,
                'disable_web_page_preview' => true,
            ]],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendDeadlineWarningCommandWithIncorrectDaysArg(): void {
        $job = new SendDeadlineWarningCommand();

        $notification = $job->getNotification(['days' => 10]);

        $this->assertNull($notification);
    }

    public function testSendDeadlineWarningCommandWhenThereIsNoDeadline(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];

        $job = new SendDeadlineWarningCommand();

        $notification = $job->getNotification(['days' => 3]);

        $this->assertNull($notification);
    }

    public function testSendDeadlineWarningCommandNotification(): void {
        $date_utils = new DateUtils('2020-03-10 16:00:00'); // 3 days before deadline
        $user = FakeUser::defaultUser();

        $job = new SendDeadlineWarningCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['days' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Folgende Meldeschlüsse stehen bevor:

            - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'

            ZZZZZZZZZZ;
        $this->assertSame('Meldeschlusswarnung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
