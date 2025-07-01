<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendWeeklySummaryCommand;
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
 * @covers \Olz\Command\Notifications\SendWeeklySummaryCommand
 */
final class SendWeeklySummaryCommandTest extends UnitTestCase {
    public function testSendWeeklySummaryCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $artifacts = [];
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

        $job = new SendWeeklySummaryCommand();
        $job->setDateUtils(new DateUtils('2020-03-16 16:00:00'));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendWeeklySummaryCommand...',
            'INFO Sending \'weekly_summary\' notifications...',
            'INFO Getting notification for \'{"aktuell":true,"blog":true,"galerie":true,"forum":true}\'...',
            'INFO Sending notification Wochenzusammenfassung over email to user (2)...',
            'DEBUG Sending email to "Admin Istrator" <admin-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (2): Wochenzusammenfassung',
            'INFO Sending notification Wochenzusammenfassung over invalid-delivery to user (2)...',
            'CRITICAL Unknown delivery type \'invalid-delivery\'',
            'INFO Sending notification Wochenzusammenfassung over telegram to user (666)...',
            'NOTICE Error sending telegram to user (666): [Exception] provoked telegram error',
            'INFO Sending notification Wochenzusammenfassung over telegram to user (404)...',
            'NOTICE User (404) has no telegram link, but a subscription (22)',
            'INFO Getting notification for \'{"no_notification":true}\'...',
            'INFO Nothing to send.',
            'INFO Getting notification for \'{"provoke_error":true}\'...',
            'INFO Nothing to send.',
            'INFO Successfully ran command Olz\Command\Notifications\SendWeeklySummaryCommand.',
        ], $this->getLogs());

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Admin Istrator" <admin-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Wochenzusammenfassung

                Hallo Admin,

                Das lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):


                **Aktuell**

                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)



                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoid2Vla2x5X3N1bW1hcnkifQ
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Admin,

                Das lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):


                **Aktuell**

                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlIjoid2Vla2x5X3N1bW1hcnkifQ">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjIsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendWeeklySummaryCommandWrongWeekday(): void {
        $date_utils = new DateUtils('2020-03-13 16:00:00'); // a Friday

        $job = new SendWeeklySummaryCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $this->assertNull($notification);
    }

    public function testSendWeeklySummaryCommandWithAllContent(): void {
        $date_utils = new DateUtils('2020-03-16 16:00:00'); // a Monday
        $user = FakeUser::defaultUser();

        $job = new SendWeeklySummaryCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Das lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Aktuell**

            - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Aktualisierte Termine**
            
            - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)

            
            ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testSendWeeklySummaryCommandWithNoContent(): void {
        $date_utils = new DateUtils('2020-03-16 16:00:00'); // a Monday

        $job = new SendWeeklySummaryCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertNull($notification);
    }
}
