<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Entity\SolvEvent;
use Olz\Entity\Termine\Termin;
use Olz\Entity\User;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter
 */
final class DeadlineWarningGetterTest extends UnitTestCase {
    public function testDeadlineWarningGetterWithIncorrectDaysArg(): void {
        $job = new DeadlineWarningGetter();

        $notification = $job->getDeadlineWarningNotification(['days' => 10]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetterWhenThereIsNoDeadline(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $entity_manager->repositories[SolvEvent::class]->entitiesToBeMatched = [];
        $entity_manager->repositories[Termin::class]->entitiesToBeMatched = [];
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();

        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $this->assertSame(null, $notification);
    }

    public function testDeadlineWarningGetter(): void {
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();

        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Folgende Meldeschlüsse stehen bevor:
        
        - 01.01.: Meldeschluss für '[Cannot be empty](http://fake-base-url/_/termine/123)'
        - 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'
        - : Meldeschluss für '[Fake title](http://fake-base-url/_/termine/12)'
        - 01.01.: Meldeschluss für '[Cannot be empty](http://fake-base-url/_/termine/123)'
        - 13.03.: Meldeschluss für '[Fake title](http://fake-base-url/_/termine/1234)'

        ZZZZZZZZZZ;
        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }
}
