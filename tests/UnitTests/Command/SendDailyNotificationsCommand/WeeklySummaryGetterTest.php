<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter
 */
final class WeeklySummaryGetterTest extends UnitTestCase {
    public function testWeeklySummaryGetterWrongWeekday(): void {
        $date_utils = new DateUtils('2020-03-13 16:00:00'); // a Friday

        $job = new WeeklySummaryGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $this->assertNull($notification);
    }

    public function testWeeklySummaryGetterWithAllContent(): void {
        $date_utils = new DateUtils('2020-03-16 16:00:00'); // a Monday
        $user = FakeUser::defaultUser();

        $job = new WeeklySummaryGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Das lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Aktuell**

            - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - Fr, 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Aktualisierte Termine**
            
            - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)

            
            ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testWeeklySummaryGetterWithNoContent(): void {
        $date_utils = new DateUtils('2020-03-16 16:00:00'); // a Monday

        $job = new WeeklySummaryGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertNull($notification);
    }
}
