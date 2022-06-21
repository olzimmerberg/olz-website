<?php

declare(strict_types=1);

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\WeeklyPreviewGetter;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Tasks\SendDailyNotificationsTask\WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterIntegrationTest extends IntegrationTestCase {
    public function testWeeklyPreviewGetter(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-08-13 16:00:00'); // a Thursday
        $logger = new Logger('WeeklyPreviewGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getWeeklyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


        **Termine**
        
        - 18.08.: [Training 1](http://integration-test.host/termine.php?id=3)
        - 22.08.: [Grossanlass](http://integration-test.host/termine.php?id=10)

        
        **Meldeschlüsse**

        - 17.08.: Meldeschluss für '[Grossanlass](http://integration-test.host/termine.php?id=10)'


        ZZZZZZZZZZ;
        $this->assertSame('Vorschau auf die Woche vom 17. August', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
