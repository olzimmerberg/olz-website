<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Olz\Command\Notifications\SendTelegramConfigurationReminderCommand;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendTelegramConfigurationReminderCommand
 */
final class SendTelegramConfigurationReminderCommandIntegrationTest extends IntegrationTestCase {
    public function testSendTelegramConfigurationReminderCommandAutogenerateSubscriptions(): void {
        $job = $this->getSut();
        $job->autogenerateSubscriptions();

        $this->assertSame([
            'INFO Generating telegram configuration reminder subscriptions...',
        ], $this->getLogs());
    }

    public function testSendTelegramConfigurationReminderCommand(): void {
        $the_day = SendTelegramConfigurationReminderCommand::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new DateUtils("2020-07-{$the_day_str} 16:00:00");
        $user = FakeUser::defaultUser();

        $job = $this->getSut();
        $job->setDateUtils($date_utils);
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.


            **Du möchtest eigentlich Push-Nachrichten erhalten?**

            In diesem Fall musst du dich auf der Website *einloggen*, und im ["Newsletter"-App](http://integration-test.host/apps/newsletter) (ist auch unter "Service" zu finden) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


            **Du möchtest gar keine Push-Nachrichten erhalten?**

            Dann lösche einfach diesen Chat.


            ZZZZZZZZZZ;
        $this->assertSame([], $this->getLogs());
        $this->assertSame('Keine Push-Nachrichten abonniert', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    protected function getSut(): SendTelegramConfigurationReminderCommand {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SendTelegramConfigurationReminderCommand::class);
    }
}
