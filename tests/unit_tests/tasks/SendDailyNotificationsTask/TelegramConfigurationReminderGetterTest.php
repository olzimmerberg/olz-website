<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/SolvEvent.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/TelegramConfigurationReminderGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \TelegramConfigurationReminderGetter
 */
final class TelegramConfigurationReminderGetterTest extends UnitTestCase {
    public function testTelegramConfigurationReminderGetterOnWrongDay(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // not the first day of the month
        $logger = new Logger('TelegramConfigurationReminderGetterTest');

        $job = new TelegramConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification([]);

        $this->assertSame(null, $notification);
    }

    public function testTelegramConfigurationReminderGetter(): void {
        $date_utils = new FixedDateUtils('2020-03-01 19:00:00'); // the first day of the month
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('TelegramConfigurationReminderGetterTest');
        $user = new User();
        $user->setFirstName('First');

        $job = new TelegramConfigurationReminderGetter();
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Du hast bisher keinerlei Push-Nachrichten für Telegram abonniert.


        **Du möchtest eigentlich Push-Nachrichten erhalten?**
        
        In diesem Fall musst du dich auf der Website *einloggen*, und unter ["Service"](http://fake-base-url/_/service.php) bei "Nachrichten-Push" die gewünschten Benachrichtigungen auswählen.


        **Du möchtest gar keine Push-Nachrichten erhalten?**

        Dann lösche einfach diesen Chat.


        ZZZZZZZZZZ;
        $this->assertSame('Keine Push-Nachrichten abonniert', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
