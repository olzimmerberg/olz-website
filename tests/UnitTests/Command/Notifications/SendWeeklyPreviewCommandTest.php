<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\Notifications;

use Olz\Command\Notifications\SendWeeklyPreviewCommand;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendWeeklyPreviewCommand
 */
final class SendWeeklyPreviewCommandTest extends UnitTestCase {
    public function testSendWeeklyPreviewCommand(): void {
        $mailer = $this->createMock(MailerInterface::class);
        WithUtilsCache::get('emailUtils')->setMailer($mailer);
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $mailer->expects($this->exactly(0))->method('send');

        $job = new SendWeeklyPreviewCommand();
        $job->setDateUtils(new DateUtils('2020-03-12 16:00:00')); // a Thursday
        $job->run($input, $output);

        $this->assertSame([
            'INFO Running command Olz\Command\Notifications\SendWeeklyPreviewCommand...',
            'INFO Sending \'weekly_preview\' notifications...',
            'INFO Getting notification for \'[]\'...',
            'INFO Sending notification Vorschau auf die Woche vom 16. März over telegram to user (1)...',
            'INFO Telegram sent to user (1): Vorschau auf die Woche vom 16. März',
            'INFO Getting notification for \'{"no_notification":true}\'...',
            'INFO Sending notification Vorschau auf die Woche vom 16. März over telegram to user (1)...',
            'INFO Telegram sent to user (1): Vorschau auf die Woche vom 16. März',
            'INFO Successfully ran command Olz\Command\Notifications\SendWeeklyPreviewCommand.',
        ], $this->getLogs());

        $this->assertSame([
            ['sendMessage', [
                'chat_id' => '99999',
                'parse_mode' => 'HTML',
                'text' => <<<'ZZZZZZZZZZ'
                    <b>Vorschau auf die Woche vom 16. März</b>

                    Hallo Default,

                    Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


                    **Termine**

                    - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
                    - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


                    **Meldeschlüsse**

                    - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


                    ZZZZZZZZZZ,
                'disable_web_page_preview' => true,
            ]],
            ['sendMessage', [
                'chat_id' => '99999',
                'parse_mode' => 'HTML',
                'text' => <<<'ZZZZZZZZZZ'
                    <b>Vorschau auf die Woche vom 16. März</b>

                    Hallo Default,

                    Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


                    **Termine**

                    - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
                    - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


                    **Meldeschlüsse**

                    - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'

                    
                    ZZZZZZZZZZ,
                'disable_web_page_preview' => true,
            ]],
        ], WithUtilsCache::get('telegramUtils')->telegramApiCalls);
    }

    public function testSendWeeklyPreviewCommandOnWrongWeekday(): void {
        $date_utils = new DateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new SendWeeklyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertNull($notification);
    }

    public function testSendWeeklyPreviewCommandNotification(): void {
        $date_utils = new DateUtils('2020-03-12 16:00:00'); // a Thursday
        $user = FakeUser::defaultUser();

        $job = new SendWeeklyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


            **Termine**

            - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
            - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            **Meldeschlüsse**

            - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Vorschau auf die Woche vom 16. März', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptySendWeeklyPreviewCommand(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $date_utils = new DateUtils('2021-03-18 16:00:00'); // a Thursday

        $job = new SendWeeklyPreviewCommand();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }
}
