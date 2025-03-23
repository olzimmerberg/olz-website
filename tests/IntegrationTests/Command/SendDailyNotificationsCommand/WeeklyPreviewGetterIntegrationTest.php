<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\SendDailyNotificationsCommand;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\SendDailyNotificationsCommand\WeeklyPreviewGetter
 */
final class WeeklyPreviewGetterIntegrationTest extends IntegrationTestCase {
    public function testWeeklyPreviewGetter(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-08-13 16:00:00'); // a Thursday
        $user = FakeUser::defaultUser();

        $job = new WeeklyPreviewGetter();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification([]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Bis Ende nächster Woche haben wir Folgendes auf dem Programm:


            **Termine**

            - So, 16.08. - Mo, 17.08.: [24h-OL](http://integration-test.host/termine/12)
            - Di, 18.08.: [Training 1](http://integration-test.host/termine/3)
            - Sa, 22.08.: [Grossanlass](http://integration-test.host/termine/10)


            **Meldeschlüsse**

            - Mo, 17.08.: Meldeschluss für '[Training 1](http://integration-test.host/termine/3)'


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Vorschau auf die Woche vom 17. August', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    protected function getEntityManager(): EntityManagerInterface {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
