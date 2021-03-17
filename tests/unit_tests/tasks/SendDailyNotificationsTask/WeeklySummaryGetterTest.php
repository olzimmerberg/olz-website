<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/Termin.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/tasks/SendDailyNotificationsTask/WeeklySummaryGetter.php';
require_once __DIR__.'/../../../../src/utils/date/FixedDateUtils.php';

class FakeWeeklySummaryGetterEntityManager {
    public $repositories = [];

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }
}

class FakeWeeklySummaryGetterAktuellRepository {
    public function matching($criteria) {
        $aktuell1 = new Aktuell();
        $aktuell1->setId(1);
        $aktuell1->setDate(new DateTime('2020-03-12'));
        $aktuell1->setTime(new DateTime('22:00:00'));
        $aktuell1->setTitle('Bericht vom Lauftraining');
        $aktuell2 = new Aktuell();
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

/**
 * @internal
 * @covers \WeeklySummaryGetter
 */
final class WeeklySummaryGetterTest extends TestCase {
    public function testWeeklySummaryGetterWrongWeekday(): void {
        $entity_manager = new FakeWeeklySummaryGetterEntityManager();
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
        ]);

        $this->assertSame(null, $notification);
    }

    public function testWeeklySummaryGetterWithAllContent(): void {
        $entity_manager = new FakeWeeklySummaryGetterEntityManager();
        $aktuell_repo = new FakeWeeklySummaryGetterAktuellRepository();
        $blog_repo = new FakeWeeklySummaryGetterBlogRepository();
        $galerie_repo = new FakeWeeklySummaryGetterGalerieRepository();
        $forum_repo = new FakeWeeklySummaryGetterForumRepository();
        $entity_manager->repositories['Aktuell'] = $aktuell_repo;
        $entity_manager->repositories['Blog'] = $blog_repo;
        $entity_manager->repositories['Galerie'] = $galerie_repo;
        $entity_manager->repositories['Forum'] = $forum_repo;
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
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
        ]);

        $this->assertSame('Wochenzusammenfassung', $notification->title);
        $this->assertSame("Hallo First,\n\nDas lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):\n\n\n**Aktuell**\n\n- 12.03. 22:00: Bericht vom Lauftraining\n- 13.03. 16:00: MV nicht abgesagt!\n\n\n**Kaderblog**\n\n- 12.03. 22:00: Bericht vom Lauftraining\n- 13.03. 16:00: MV nicht abgesagt!\n\n\n**Galerien**\n\n- 12.03.: Bericht vom Lauftraining\n- 13.03.: MV nicht abgesagt!\n\n\n**Forum**\n\n- 12.03. 22:00: Bericht vom Lauftraining\n- 13.03. 16:00: MV nicht abgesagt!\n\n", $notification->getTextForUser($user));
    }

    public function testWeeklySummaryGetterWithNoContent(): void {
        $entity_manager = new FakeWeeklySummaryGetterEntityManager();
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $logger = new Logger('WeeklySummaryGetterTest');
        // $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout', Logger::INFO));
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);
        $job->setLogger($logger);
        $notification = $job->getWeeklySummaryNotification([]);

        $this->assertSame(null, $notification);
    }
}
