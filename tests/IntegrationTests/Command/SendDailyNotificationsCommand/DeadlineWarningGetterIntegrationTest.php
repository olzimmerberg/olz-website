<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter
 */
final class DeadlineWarningGetterIntegrationTest extends IntegrationTestCase {
    public function testDeadlineWarningGetter(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new DateUtils('2020-08-15 19:30:00');
        $user = FakeUser::defaultUser();

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification(['days' => 2]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Folgende Meldeschlüsse stehen bevor:

            - Mo, 17.08.: Meldeschluss für '[Training 1](http://integration-test.host/termine/3)'

            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Meldeschlusswarnung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDeadlineWarningGetterNone(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new DateUtils('2020-08-15 19:30:00');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification(['days' => 3]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }
}
