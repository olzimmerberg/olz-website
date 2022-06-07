<?php

declare(strict_types=1);

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../../../_/tasks/SendDailyNotificationsTask/DailySummaryGetter.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \DailySummaryGetter
 */
final class DailySummaryGetterIntegrationTest extends IntegrationTestCase {
    public function testDailySummaryGetterDay1(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-01-01 12:51:00');
        $logger = new Logger('DailySummaryGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
        **Aktuell**
        
        - 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/aktuell.php?id=3)
        
        
        **Kaderblog**
        
        - 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/blog.php#id1)
        

        **Aktualisierte Termine**
        
        - 06.06.: [Brunch OL](http://integration-test.host/termine.php?id=2)
        
        
        ZZZZZZZZZZ;
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterDay2(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-01-02 12:51:00');
        $logger = new Logger('DailySummaryGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
        **Galerien**
        
        - 01.01.: [Neujahrsgalerie 📷 2020](http://integration-test.host/galerie.php?id=1)
        
        
        **Forum**
        
        - 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/forum.php#id1)

        
        **Aktualisierte Termine**

        - 02.01.: [Berchtoldstag 🥈](http://integration-test.host/termine.php?id=1)


        ZZZZZZZZZZ;
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterDay3(): void {
        global $entityManager;
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';

        $date_utils = new FixedDateUtils('2020-01-03 12:51:00');
        $logger = new Logger('DailySummaryGetterIntegrationTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
        **Galerien**
        
        - 02.01.: [Berchtoldstagsgalerie 2020](http://integration-test.host/galerie.php?id=2)
        

        ZZZZZZZZZZ;
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
