<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\MonthlyPreviewGetter
 */
final class MonthlyPreviewGetterTest extends UnitTestCase {
    public function testMonthlyPreviewGetterOnWrongWeekday(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    public function testMonthlyPreviewGetterTooEarlyInMonth(): void {
        $date_utils = new FixedDateUtils('2020-03-14 16:00:00'); // a Saturday, but not yet the second last

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    public function testMonthlyPreviewGetterTooLateInMonth(): void {
        $date_utils = new FixedDateUtils('2020-03-28 16:00:00'); // a Saturday, but already the last

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    public function testMonthlyPreviewGetter(): void {
        $date_utils = new FixedDateUtils('2020-03-21 16:00:00'); // the second last Saturday of the month
        $user = FakeUser::defaultUser();

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Im April haben wir Folgendes auf dem Programm:


            **Termine**

            - 13.03.: [Fake title](http://fake-base-url/_/termine/12)
            - 01.01.: [Cannot be empty](http://fake-base-url/_/termine/123)
            - 13.03. - 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            **Meldeschlüsse**

            - : Meldeschluss für '[Fake title](http://fake-base-url/_/termine/12)'
            - 01.01.: Meldeschluss für '[Cannot be empty](http://fake-base-url/_/termine/123)'
            - 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Monatsvorschau April', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyMonthlyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];
        $date_utils = new FixedDateUtils('2021-03-20 16:00:00'); // the second last Saturday of the month

        $job = new MonthlyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }
}
