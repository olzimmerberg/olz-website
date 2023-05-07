<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\TelegramConfigurationReminderGetter
 */
final class TelegramConfigurationReminderGetterIntegrationTest extends IntegrationTestCase {
    public function testTelegramConfigurationReminderGetter(): void {
        $the_day = TelegramConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-07-{$the_day_str} 16:00:00");
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new TelegramConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.


        **Du möchtest eigentlich Push-Nachrichten erhalten?**
        
        In diesem Fall musst du dich auf der Website *einloggen*, und im ["Newsletter"-App](http://integration-test.host/apps/newsletter) (ist auch unter "Service" zu finden) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


        **Du möchtest gar keine Push-Nachrichten erhalten?**

        Dann lösche einfach diesen Chat.


        ZZZZZZZZZZ;
        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame('Keine Push-Nachrichten abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
