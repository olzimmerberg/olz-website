<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter;
use Olz\Entity\User;
use Olz\Tests\Fake;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\DeadlineWarningGetter
 */
final class DeadlineWarningGetterIntegrationTest extends IntegrationTestCase {
    public function testDeadlineWarningGetter(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-08-15 19:30:00');
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 2]);

        $expected_text = <<<'ZZZZZZZZZZ'
        Hallo First,
        
        Folgende Meldeschlüsse stehen bevor:
        
        - 17.08.: Meldeschluss für '[Grossanlass](http://integration-test.host/termine/10)'
        - 17.08.: Meldeschluss für '[Training 1](http://integration-test.host/termine/3)'

        ZZZZZZZZZZ;
        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame('Meldeschlusswarnung', $notification->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    public function testDeadlineWarningGetterNone(): void {
        $entityManager = DbUtils::fromEnv()->getEntityManager();
        $date_utils = new FixedDateUtils('2020-08-15 19:30:00');
        $logger = Fake\FakeLogger::create();
        $user = new User();
        $user->setFirstName('First');

        $job = new DeadlineWarningGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(EnvUtils::fromEnv());
        $job->setLogger($logger);
        $notification = $job->getDeadlineWarningNotification(['days' => 3]);

        $this->assertSame([
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(null, $notification);
    }
}
