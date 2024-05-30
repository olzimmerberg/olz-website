<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterTest extends UnitTestCase {
    public function testWeeklyPreviewGetterOnWrongWeekday(): void {
        $date_utils = new FixedDateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklyPreviewNotification();

        $this->assertNull($notification);
    }

    public function testWeeklyPreviewGetter(): void {
        $date_utils = new FixedDateUtils('2020-03-19 16:00:00'); // a Thursday
        $user = FakeUser::defaultUser();

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklyPreviewNotification();

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


            **Termine**

            - 13.03.: [Fake title](http://fake-base-url/_/termine/12)
            - 01.01.: [Cannot be empty](http://fake-base-url/_/termine/123)
            - 13.03. - 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            **Meldeschlüsse**

            - 01.01.: Meldeschluss für '[Cannot be empty](http://fake-base-url/_/termine/123)'
            - 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'
            - : Meldeschluss für '[Fake title](http://fake-base-url/_/termine/12)'
            - 01.01.: Meldeschluss für '[Cannot be empty](http://fake-base-url/_/termine/123)'
            - 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Vorschau auf die Woche vom 23. März', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyWeeklyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $date_utils = new FixedDateUtils('2021-03-18 16:00:00'); // a Thursday

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getWeeklyPreviewNotification();

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }
}
