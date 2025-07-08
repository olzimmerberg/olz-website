<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendDailySummaryCommand;
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
 * @covers \Olz\Command\Notifications\SendDailySummaryCommand
 */
final class SendDailySummaryCommandTest extends UnitTestCase {
    public function testSendDailySummaryCommand(): void {
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

        $job = new SendDailySummaryCommand();
        $job->setDateUtils(new DateUtils('2020-03-14 18:00:00'));
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendDailySummaryCommand...',
            'INFO Sending \'daily_summary\' notifications...',
            'INFO Getting notification for \'{"aktuell":true,"blog":true,"galerie":true,"forum":true}\'...',
            'INFO Sending notification Tageszusammenfassung over email to user (1)...',
            'DEBUG Sending email to "Default User" <default-user@staging.olzimmerberg.ch> ()',
            'INFO Email sent to user (1): Tageszusammenfassung',
            'INFO Getting notification for \'{"no_notification":true}\'...',
            'INFO Nothing to send.',
            'INFO Successfully ran command Olz\Command\Notifications\SendDailySummaryCommand.',
        ], $this->getLogs());

        $this->assertSame([
            <<<'ZZZZZZZZZZ'
                From: 
                Reply-To: 
                To: "Default User" <default-user@staging.olzimmerberg.ch>
                Cc: 
                Bcc: 
                Subject: [OLZ] Tageszusammenfassung

                Hallo Default,
                
                Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
                
                
                **Aktuell**
                
                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)
                
                
                
                ---
                Abmelden?
                Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGFpbHlfc3VtbWFyeSJ9
                Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0

                <div style="text-align: right; float: right;">
                    <img src="cid:olz_logo" alt="" style="width:150px;" />
                </div>
                <br /><br /><br />
                Hallo Default,
                
                Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
                
                
                **Aktuell**
                
                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
                - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)
                

                <br /><br />
                <hr style="border: 0; border-top: 1px solid black;">
                Abmelden? <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlIjoiZGFpbHlfc3VtbWFyeSJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOjEsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>

                olz_logo
                ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));

        $this->assertSame([], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendDailySummaryCommandWithAllContent(): void {
        $date_utils = new DateUtils('2020-03-14 18:00:00');
        $user = FakeUser::defaultUser();

        $job = new SendDailySummaryCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Aktuell**

            - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Aktualisierte Termine**

            - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            ZZZZZZZZZZ;
        $this->assertSame('Tageszusammenfassung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testSendDailySummaryCommandWithNoContent(): void {
        $date_utils = new DateUtils('2020-03-21 16:00:00'); // a Saturday

        $job = new SendDailySummaryCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertNull($notification);
    }
}
