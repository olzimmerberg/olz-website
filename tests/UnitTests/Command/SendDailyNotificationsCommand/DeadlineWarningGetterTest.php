<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter
 */
final class DeadlineWarningGetterTest extends UnitTestCase {
    public function testDeadlineWarningGetterWithIncorrectDaysArg(): void {
        $job = new DeadlineWarningGetter();

        $notification = $job->getNotification(['days' => 10]);

        $this->assertNull($notification);
    }

    public function testDeadlineWarningGetterWhenThereIsNoDeadline(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];

        $job = new DeadlineWarningGetter();

        $notification = $job->getNotification(['days' => 3]);

        $this->assertNull($notification);
    }

    public function testDeadlineWarningGetter(): void {
        $date_utils = new DateUtils('2020-03-10 16:00:00'); // 3 days before deadline
        $user = FakeUser::defaultUser();

        $job = new DeadlineWarningGetter();
        $job->setDateUtils($date_utils);

        $notification = $job->getNotification(['days' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Folgende Meldeschlüsse stehen bevor:

            - Fr, 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'

            ZZZZZZZZZZ;
        $this->assertSame('Meldeschlusswarnung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
