<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../_/termine/model/Termin.php';
require_once __DIR__.'/../../../../_/model/User.php';
require_once __DIR__.'/../../../../_/tasks/SendDailyNotificationsTask/WeeklySummaryGetter.php';
require_once __DIR__.'/../../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeWeeklySummaryGetterNewsRepository {
    public function matching($criteria) {
        $aktuell1 = new NewsEntry();
        $aktuell1->setId(1);
        $aktuell1->setDate(new DateTime('2020-03-12'));
        $aktuell1->setTime(new DateTime('22:00:00'));
        $aktuell1->setTitle('Bericht vom Lauftraining');
        $aktuell2 = new NewsEntry();
        $aktuell2->setId(2);
        $aktuell2->setDate(new DateTime('2020-03-13'));
        $aktuell2->setTime(new DateTime('16:00:00'));
        $aktuell2->setTitle('MV nicht abgesagt!');
        return [$aktuell1, $aktuell2];
    }
}

class FakeWeeklySummaryGetterBlogRepository {
    public function matching($criteria) {
        $blog1 = new Blog();
        $blog1->setId(1);
        $blog1->setDate(new DateTime('2020-03-12'));
        $blog1->setTime(new DateTime('22:00:00'));
        $blog1->setTitle('Bericht vom Lauftraining');
        $blog2 = new Blog();
        $blog2->setId(2);
        $blog2->setDate(new DateTime('2020-03-13'));
        $blog2->setTime(new DateTime('16:00:00'));
        $blog2->setTitle('MV nicht abgesagt!');
        return [$blog1, $blog2];
    }
}

class FakeWeeklySummaryGetterGalerieRepository {
    public function matching($criteria) {
        $galerie1 = new Galerie();
        $galerie1->setId(1);
        $galerie1->setDate(new DateTime('2020-03-12'));
        $galerie1->setTitle('Bericht vom Lauftraining');
        $galerie2 = new Galerie();
        $galerie2->setId(2);
        $galerie2->setDate(new DateTime('2020-03-13'));
        $galerie2->setTitle('MV nicht abgesagt!');
        return [$galerie1, $galerie2];
    }
}

class FakeWeeklySummaryGetterForumRepository {
    public function matching($criteria) {
        $forum1 = new Forum();
        $forum1->setId(1);
        $forum1->setDate(new DateTime('2020-03-12'));
        $forum1->setTime(new DateTime('22:00:00'));
        $forum1->setTitle('Bericht vom Lauftraining');
        $forum2 = new Forum();
        $forum2->setId(2);
        $forum2->setDate(new DateTime('2020-03-13'));
        $forum2->setTime(new DateTime('16:00:00'));
        $forum2->setTitle('MV nicht abgesagt!');
        return [$forum1, $forum2];
    }
}

class FakeWeeklySummaryGetterTerminRepository {
    public function matching($criteria) {
        $termin1 = new Termin();
        $termin1->setId(1);
        $termin1->setStartsOn(new DateTime('2020-03-20'));
        $termin1->setTitle('4. Lauf Zürcher Nacht-OL Serie');
        $termin2 = new Termin();
        $termin2->setId(2);
        $termin2->setStartsOn(new DateTime('2020-03-22'));
        $termin2->setTitle('2. Nationaler OL (Langdistanz)');
        return [$termin1, $termin2];
    }
}

/**
 * @internal
 * @covers \WeeklySummaryGetter
 */
final class WeeklySummaryGetterTest extends UnitTestCase {
    public function testWeeklySummaryGetterWrongWeekday(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 16:00:00'); // a Friday
        $logger = new Logger('WeeklySummaryGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $this->assertSame(null, $notification);
    }

    public function testWeeklySummaryGetterWithAllContent(): void {
        $entity_manager = new FakeEntityManager();
        $news_repo = new FakeWeeklySummaryGetterNewsRepository();
        $blog_repo = new FakeWeeklySummaryGetterBlogRepository();
        $galerie_repo = new FakeWeeklySummaryGetterGalerieRepository();
        $forum_repo = new FakeWeeklySummaryGetterForumRepository();
        $termin_repo = new FakeWeeklySummaryGetterTerminRepository();
        $entity_manager->repositories['NewsEntry'] = $news_repo;
        $entity_manager->repositories['Blog'] = $blog_repo;
        $entity_manager->repositories['Galerie'] = $galerie_repo;
        $entity_manager->repositories['Forum'] = $forum_repo;
        $entity_manager->repositories['Termin'] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('WeeklySummaryGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
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
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/aktuell.php?id=1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/aktuell.php?id=2)
        
        
        **Kaderblog**
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/blog.php#id1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/blog.php#id2)
        
        
        **Galerien**
        
        - 12.03.: [Bericht vom Lauftraining](http://fake-base-url/_/galerie.php?id=1)
        - 13.03.: [MV nicht abgesagt!](http://fake-base-url/_/galerie.php?id=2)
        
        
        **Forum**
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/forum.php#id1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/forum.php#id2)

        
        **Aktualisierte Termine**
        
        - 20.03.: [4. Lauf Zürcher Nacht-OL Serie](http://fake-base-url/_/termine.php?id=1)
        - 22.03.: [2. Nationaler OL (Langdistanz)](http://fake-base-url/_/termine.php?id=2)


        ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testWeeklySummaryGetterWithNoContent(): void {
        $entity_manager = new FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $env_utils = new FakeEnvUtils();
        $logger = new Logger('WeeklySummaryGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils($env_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklySummaryNotification([]);

        $this->assertSame(null, $notification);
    }
}
