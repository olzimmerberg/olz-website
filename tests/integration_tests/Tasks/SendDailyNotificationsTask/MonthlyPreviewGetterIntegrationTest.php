<?php

declare(strict_types=1);

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\MonthlyPreviewGetter;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Tasks\SendDailyNotificationsTask\MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterIntegrationTest extends IntegrationTestCase {
    public function testMonthlyPreviewGetter(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-07-18 16:00:00'); // the second last Saturday of the month
        $logger = new Logger('MonthlyPreviewGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new MonthlyPreviewGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getMonthlyPreviewNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Im August haben wir Folgendes auf dem Programm:


        **Termine**
 
        - 04.08.: [Training -1](http://integration-test.host/termine.php?id=9)
        - 11.08.: [Training 0](http://integration-test.host/termine.php?id=8)
        - 18.08.: [Training 1](http://integration-test.host/termine.php?id=3)
        - 22.08.: [Grossanlass](http://integration-test.host/termine.php?id=10)
        - 25.08.: [Training 2](http://integration-test.host/termine.php?id=4)
        - 26.08.: [Milchsuppen-Cup, OLZ Trophy 4. Lauf](http://integration-test.host/termine.php?id=5)


        **Meldeschlüsse**

        - 17.08.: Meldeschluss für '[Grossanlass](http://integration-test.host/termine.php?id=10)'


        ZZZZZZZZZZ;
        $this->assertSame('Monatsvorschau August', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
