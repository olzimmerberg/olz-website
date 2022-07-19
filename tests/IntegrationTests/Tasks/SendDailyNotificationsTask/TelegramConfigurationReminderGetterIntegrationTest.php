<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Tasks\SendDailyNotificationsTask;

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\TelegramConfigurationReminderGetter;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 * @covers \Olz\Tasks\SendDailyNotificationsTask\TelegramConfigurationReminderGetter
 */
final class TelegramConfigurationReminderGetterIntegrationTest extends IntegrationTestCase {
    public function testTelegramConfigurationReminderGetter(): void {
        $the_day = TelegramConfigurationReminderGetter::DAY_OF_MONTH;
        $the_day_str = str_pad("{$the_day}", 2, '0', STR_PAD_LEFT);
        $date_utils = new FixedDateUtils("2020-07-{$the_day_str} 16:00:00");
        $logger = new Logger('TelegramConfigurationReminderGetterIntegrationTest');
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
        
        In diesem Fall musst du dich auf der Website *einloggen*, und unter ["Service"](http://integration-test.host/service.php) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


        **Du möchtest gar keine Push-Nachrichten erhalten?**

        Dann lösche einfach diesen Chat.


        ZZZZZZZZZZ;
        $this->assertSame('Keine Push-Nachrichten abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
