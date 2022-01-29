<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/termine/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/MonthlyPreviewGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterIntegrationTest extends IntegrationTestCase {
    public function testMonthlyPreviewGetter(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../src/config/doctrine_db.php';

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
 
        - 04.08.: [Training -1](http://integration-test.host/_/termine.php#id9)
        - 11.08.: [Training 0](http://integration-test.host/_/termine.php#id8)
        - 18.08.: [Training 1](http://integration-test.host/_/termine.php#id3)
        - 22.08.: [Grossanlass](http://integration-test.host/_/termine.php#id10)
        - 25.08.: [Training 2](http://integration-test.host/_/termine.php#id4)
        - 26.08.: [Milchsuppen-Cup, OLZ Trophy 4. Lauf](http://integration-test.host/_/termine.php#id5)


        **Meldeschlüsse**

        - 17.08.: Meldeschluss für '[Grossanlass](http://integration-test.host/_/termine.php#id10)'


        ZZZZZZZZZZ;
        $this->assertSame('Monatsvorschau August', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
