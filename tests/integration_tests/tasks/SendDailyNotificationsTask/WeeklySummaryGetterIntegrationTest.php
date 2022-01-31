<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/termine/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/WeeklySummaryGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \WeeklySummaryGetter
 */
final class WeeklySummaryGetterIntegrationTest extends IntegrationTestCase {
    public function testWeeklySummaryGetter(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../src/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-01-06 16:00:00'); // a Monday
        $logger = new Logger('WeeklySummaryGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getWeeklySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
        **Aktuell**
        
        - 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/_/aktuell.php?id=3)
        
        
        **Kaderblog**
        
        - 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/_/blog.php#id1)
        
        
        **Galerien**
        
        - 01.01.: [Neujahrsgalerie 📷 2020](http://integration-test.host/_/galerie.php?id=1)
        - 02.01.: [Berchtoldstagsgalerie 2020](http://integration-test.host/_/galerie.php?id=2)
        
        
        **Forum**
        
        - 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/_/forum.php#id1)
        - 03.01. 18:42: [Verspätete Neujahrsgrüsse](http://integration-test.host/_/forum.php#id2)
        - 06.01. 06:07: [Hallo](http://integration-test.host/_/forum.php#id3)
        
        
        **Aktualisierte Termine**
        
        - 02.01.: [Berchtoldstag 🥈](http://integration-test.host/_/termine.php?id=1)
        - 06.06.: [Brunch OL](http://integration-test.host/_/termine.php?id=2)
        
        
        ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
