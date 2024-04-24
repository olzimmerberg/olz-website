<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DailySummaryGetter
 */
final class DailySummaryGetterTest extends UnitTestCase {
    public function testDailySummaryGetterWithAllContent(): void {
        $date_utils = new FixedDateUtils('2020-03-13 16:00:00'); // a Saturday
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setDateUtils($date_utils);

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
        $this->assertSame('Tageszusammenfassung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDailySummaryGetterWithNoContent(): void {
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // a Saturday
        $user = new User();
        $user->setFirstName('First');

        $job = new DailySummaryGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getDailySummaryNotification([]);

        $this->assertSame(null, $notification);
    }
}
