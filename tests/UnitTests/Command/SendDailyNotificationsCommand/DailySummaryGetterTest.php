<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

class FakeDailySummaryGetterNewsRepository {
    public function matching($criteria) {
        $aktuell1 = new NewsEntry();
        $aktuell1->setId(1);
        $aktuell1->setPublishedDate(new \DateTime('2020-03-12'));
        $aktuell1->setPublishedTime(new \DateTime('22:00:00'));
        $aktuell1->setTitle('Bericht vom Lauftraining');
        $aktuell2 = new NewsEntry();
        $aktuell2->setId(2);
        $aktuell2->setPublishedDate(new \DateTime('2020-03-13'));
        $aktuell2->setPublishedTime(new \DateTime('16:00:00'));
        $aktuell2->setTitle('MV nicht abgesagt!');
        return [$aktuell1, $aktuell2];
    }
}

class FakeDailySummaryGetterTerminRepository {
    public function matching($criteria) {
        $termin1 = new Termin();
        $termin1->setId(1);
        $termin1->setStartDate(new \DateTime('2020-03-20'));
        $termin1->setTitle('4. Lauf Zürcher Nacht-OL Serie');
        $termin2 = new Termin();
        $termin2->setId(2);
        $termin2->setStartDate(new \DateTime('2020-03-22'));
        $termin2->setEndDate(new \DateTime('2020-03-23'));
        $termin2->setTitle('2. Nationaler OL (Langdistanz)');
        return [$termin1, $termin2];
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter
 */
final class DailySummaryGetterTest extends UnitTestCase {
    public function testDailySummaryGetterWithAllContent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $news_repo = new FakeDailySummaryGetterNewsRepository();
        $termin_repo = new FakeDailySummaryGetterTerminRepository();
        $entity_manager->repositories[NewsEntry::class] = $news_repo;
        $entity_manager->repositories[Termin::class] = $termin_repo;
        $date_utils = new FixedDateUtils('2020-03-13 16:00:00'); // a Saturday
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();

        $notification = $job->getDailySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Das lief heute auf [olzimmerberg.ch](https://olzimmerberg.ch):
        
        
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
        
        - 20.03.: [4. Lauf Zürcher Nacht-OL Serie](http://fake-base-url/_/termine/1)
        - 22.03. - 23.03.: [2. Nationaler OL (Langdistanz)](http://fake-base-url/_/termine/2)


        ZZZZZZZZZZ;
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterWithNoContent(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // a Saturday
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();

        $notification = $job->getDailySummaryNotification([]);

        $this->assertSame(null, $notification);
    }
}
