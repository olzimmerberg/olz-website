<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\Notifications\SendDeadlineWarningCommand;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendDeadlineWarningCommand
 */
final class SendDeadlineWarningCommandIntegrationTest extends IntegrationTestCase {
    public function testSendDeadlineWarningCommand(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-08-15 19:30:00');
        $user = FakeUser::defaultUser();

        $job = new SendDeadlineWarningCommand();
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

    public function testSendDeadlineWarningCommandNone(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-08-15 19:30:00');

        $job = new SendDeadlineWarningCommand();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification(['days' => 3]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertNull($notification);
    }

    protected function getEntityManager(): EntityManagerInterface {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
