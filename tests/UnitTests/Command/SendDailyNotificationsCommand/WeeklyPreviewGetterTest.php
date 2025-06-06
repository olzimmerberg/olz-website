<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterTest extends UnitTestCase {
    public function testWeeklyPreviewGetterOnWrongWeekday(): void {
        $date_utils = new DateUtils('2020-03-13 19:30:00'); // a Friday

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertNull($notification);
    }

    public function testWeeklyPreviewGetter(): void {
        $date_utils = new DateUtils('2020-03-12 16:00:00'); // a Thursday
        $user = FakeUser::defaultUser();

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


            **Termine**

            - Fr, 13.03.: [Fake title](http://fake-base-url/_/termine/12)
            - Fr, 13.03. - Mo, 16.03.: [Fake title](http://fake-base-url/_/termine/1234)


            **Meldeschlüsse**

            - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Vorschau auf die Woche vom 16. März', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testEmptyWeeklyPreviewGetter(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $date_utils = new DateUtils('2021-03-18 16:00:00'); // a Thursday

        $job = new WeeklyPreviewGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification([]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }
}
