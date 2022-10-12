<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Tasks\SendDailyNotificationsTask;

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Tasks\SendDailyNotificationsTask\WeeklySummaryGetter;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Tasks\SendDailyNotificationsTask\WeeklySummaryGetter
 */
final class WeeklySummaryGetterIntegrationTest extends IntegrationTestCase {
    public function testWeeklySummaryGetter(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
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
        
        - 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/aktuell.php?id=3)
        
        
        **Kaderblog**
        
        - 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/blog.php#id1)
        
        
        **Galerien**
        
        - 01.01.: [Neujahrsgalerie 📷 2020](http://integration-test.host/galerie.php?id=1)
        - 02.01.: [Berchtoldstagsgalerie 2020](http://integration-test.host/galerie.php?id=2)
        
        
        **Forum**
        
        - 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/forum.php#id1)
        - 03.01. 18:42: [Verspätete Neujahrsgrüsse](http://integration-test.host/forum.php#id2)
        - 06.01. 06:07: [Hallo](http://integration-test.host/forum.php#id3)
        
        
        **Aktualisierte Termine**
        
        - 02.01.: [Berchtoldstag 🥈](http://integration-test.host/termine.php?id=1)
        - 06.06.: [Brunch OL](http://integration-test.host/termine.php?id=2)
        
        
        ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
