<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command\Notifications;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Command\Notifications\SendWeeklySummaryCommand;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\DateUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Command\Notifications\SendWeeklySummaryCommand
 */
final class SendWeeklySummaryCommandIntegrationTest extends IntegrationTestCase {
    public function testSendWeeklySummaryCommand(): void {
        $entityManager = $this->getEntityManager();
        $date_utils = new DateUtils('2020-01-06 16:00:00'); // a Monday
        $user = FakeUser::defaultUser();

        $job = new SendWeeklySummaryCommand();
        $job->setEntityManager($entityManager);
        $job->setDateUtils($date_utils);
        $job->setEnvUtils(new EnvUtils());
        $notification = $job->getNotification([
            'aktuell' => true,
            'blog' => true,
            'forum' => true,
            'galerie' => true,
            'termine' => true,
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
            Hallo Default,

            Das lief diese Woche auf [olzimmerberg.ch](https://olzimmerberg.ch):


            **Aktuell**

            - Mi, 01.01. 00:00: [Frohes neues Jahr! 🎆](http://integration-test.host/news/3)


            **Kaderblog**

            - Mi, 01.01. 15:15: [Saisonstart 2020!](http://integration-test.host/news/6403)


            **Forum**

            - Mi, 01.01. 21:45: [Guets Nois! 🎉](http://integration-test.host/news/2901)
            - Fr, 03.01. 18:42: [Verspätete Neujahrsgrüsse](http://integration-test.host/news/2902)
            - Mo, 06.01. 02:07: [Hallo](http://integration-test.host/news/2903)


            **Galerien**

            - Mi, 01.01.: [Neujahrsgalerie 📷 2020](http://integration-test.host/news/1202)
            - Do, 02.01.: [Berchtoldstagsgalerie 2020](http://integration-test.host/news/6)


            **Aktualisierte Termine**

            - Do, 02.01.: [Berchtoldstag 🥈](http://integration-test.host/termine/1)
            - Sa, 06.06.: [Brunch OL](http://integration-test.host/termine/2)


            ZZZZZZZZZZ;
        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame('Wochenzusammenfassung', $notification?->title);
        $this->assertSame($expected_text, $notification->getTextForUser($user));
    }

    protected function getEntityManager(): EntityManagerInterface {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
