<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklySummaryGetter
 */
final class WeeklySummaryGetterTest extends UnitTestCase {
    public function testWeeklySummaryGetterWrongWeekday(): void {
        $date_utils = new FixedDateUtils('2020-03-13 16:00:00'); // a Friday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklySummaryNotification([
            'aktuell' => true,
            'blog' => true,
            'galerie' => true,
            'forum' => true,
            'termine' => true,
        ]);

        $this->assertNull($notification);
    }

    public function testWeeklySummaryGetterWithAllContent(): void {
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
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

            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - 01.01. 00:00: [Cannot be empty](http://fake-base-url/_/news/123)
            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Kaderblog**

            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - 01.01. 00:00: [Cannot be empty](http://fake-base-url/_/news/123)
            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Forum**

            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - 01.01. 00:00: [Cannot be empty](http://fake-base-url/_/news/123)
            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Galerien**

            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/12)
            - 01.01. 00:00: [Cannot be empty](http://fake-base-url/_/news/123)
            - 13.03. 18:00: [Fake title](http://fake-base-url/_/news/1234)


            **Aktualisierte Termine**

            - 13.03.: [Fake title](http://fake-base-url/_/termine/12)
            - 01.01.: [Cannot be empty](http://fake-base-url/_/termine/123)
            - 13.03. - 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            ZZZZZZZZZZ;
        $this->assertSame('Wochenzusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testWeeklySummaryGetterWithNoContent(): void {
        $date_utils = new FixedDateUtils('2020-03-16 16:00:00'); // a Monday
        $user = new User();
        $user->setFirstName('First');

        $job = new WeeklySummaryGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklySummaryNotification([]);

        $this->assertNull($notification);
    }
}
