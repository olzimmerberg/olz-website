<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

class FakeWeeklySummaryGetterNewsRepository {
    public function matching($criteria) {
        $aktuell1 = new NewsEntry();
        $aktuell1->setId(1);
        $aktuell1->setDate(new \DateTime('2020-03-12'));
        $aktuell1->setTime(new \DateTime('22:00:00'));
        $aktuell1->setTitle('Bericht vom Lauftraining');
        $aktuell2 = new NewsEntry();
        $aktuell2->setId(2);
        $aktuell2->setDate(new \DateTime('2020-03-13'));
        $aktuell2->setTime(new \DateTime('16:00:00'));
        $aktuell2->setTitle('MV nicht abgesagt!');
        return [$aktuell1, $aktuell2];
    }
}

class FakeWeeklySummaryGetterTerminRepository {
    public function matching($criteria) {
        $termin1 = new Termin();
        $termin1->setId(1);
        $termin1->setStartsOn(new \DateTime('2020-03-20'));
        $termin1->setTitle('4. Lauf Zürcher Nacht-OL Serie');
        $termin2 = new Termin();
        $termin2->setId(2);
        $termin2->setStartsOn(new \DateTime('2020-03-22'));
        $termin2->setEndsOn(new \DateTime('2020-03-23'));
        $termin2->setTitle('2. Nationaler OL (Langdistanz)');
        return [$termin1, $termin2];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter
 */
final class WeeklySummaryGetterTest extends UnitTestCase {
    public function testWeeklySummaryGetterWrongWeekday(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-13 16:00:00'); // a Friday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);

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
        $entity_manager = new Fake\FakeEntityManager();
        $news_repo = new FakeWeeklySummaryGetterNewsRepository();
        $termin_repo = new FakeWeeklySummaryGetterTerminRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);

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
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/news/1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/news/2)
        
        
        **Kaderblog**
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/news/1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/news/2)
        
        
        **Forum**
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/news/1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/news/2)

        
        **Galerien**
        
        - 12.03. 22:00: [Bericht vom Lauftraining](http://fake-base-url/_/news/1)
        - 13.03. 16:00: [MV nicht abgesagt!](http://fake-base-url/_/news/2)
        
        
        **Aktualisierte Termine**
        
        - 20.03.: [4. Lauf Zürcher Nacht-OL Serie](http://fake-base-url/_/termine.php?id=1)
        - 22.03. - 23.03.: [2. Nationaler OL (Langdistanz)](http://fake-base-url/_/termine.php?id=2)


        ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testWeeklySummaryGetterWithNoContent(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setEntityManager($entity_manager);
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklySummaryNotification([]);

        $this->assertSame(null, $notification);
    }
}
