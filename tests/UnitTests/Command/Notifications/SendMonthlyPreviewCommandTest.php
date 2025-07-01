<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendMonthlyPreviewCommand;
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
 * @covers \Olz\Command\Notifications\SendMonthlyPreviewCommand
 */
final class SendMonthlyPreviewCommandTest extends UnitTestCase {
    public function testSendMonthlyPreviewCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('dateUtils')->testOnlySetDate('2020-03-14 18:00:00');
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
        $mailer->expects($this->exactly(2))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                if (str_contains($email->getSubject() ?? '', 'provoke')) {
                    throw new \Exception("provoked");
                }
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $job = new SendMonthlyPreviewCommand();
        $job->setDateUtils(new DateUtils('2020-02-22 16:00:00'));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendMonthlyPreviewCommand...',
            'INFO Sending \'monthly_preview\' notifications...',
            'INFO Getting notification for \'[]\'...',
            'INFO Sending notification Monatsvorschau März over email to user (1)...',
            'DEBUG Sending email to "Default User" <default-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (1): Monatsvorschau März',
            'INFO Getting notification for \'{"no_notification":true}\'...',
            'INFO Sending notification Monatsvorschau März over email to user (2)...',
            'DEBUG Sending email to "Admin Istrator" <admin-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (2): Monatsvorschau März',
            'INFO Successfully ran command Olz\Command\Notifications\SendMonthlyPreviewCommand.',
        ], $this->getLogs());

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Monatsvorschau März

                Hallo Default,

                Im März haben wir Folgendes auf dem Programm:


                **Termine**

                - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
                - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


                **Meldeschlüsse**

                - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'



                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Default,

                Im März haben wir Folgendes auf dem Programm:


                **Termine**

                - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
                - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


                **Meldeschlüsse**

                - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Admin Istrator" <admin-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Monatsvorschau März

                Hallo Admin,

                Im März haben wir Folgendes auf dem Programm:


                **Termine**

                - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
                - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


                **Meldeschlüsse**

                - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'



                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Admin,

                Im März haben wir Folgendes auf dem Programm:


                **Termine**

                - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
                - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


                **Meldeschlüsse**

                - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendMonthlyPreviewCommandOnWrongWeekday(): void {
        $date_utils = new DateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new SendMonthlyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    public function testSendMonthlyPreviewCommandTooEarlyInMonth(): void {
        $date_utils = new DateUtils('2020-02-15 16:00:00'); // a Saturday, but not yet the second last

        $job = new SendMonthlyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    public function testSendMonthlyPreviewCommandTooLateInMonth(): void {
        $date_utils = new DateUtils('2020-02-29 16:00:00'); // a Saturday, but already the last

        $job = new SendMonthlyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    public function testSendMonthlyPreviewCommandNotification(): void {
        $date_utils = new DateUtils('2020-02-22 16:00:00'); // the second last Saturday of the month
        $user = FakeUser::defaultUser();

        $job = new SendMonthlyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Im März haben wir Folgendes auf dem Programm:


            **Termine**

            - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
            - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            **Meldeschlüsse**

            - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Monatsvorschau März', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptySendMonthlyPreviewCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];
        $date_utils = new DateUtils('2021-03-20 16:00:00'); // the second last Saturday of the month

        $job = new SendMonthlyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }
}
