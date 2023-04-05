<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Command;

use Olz\Command\SyncSolvCommand;
use Olz\Entity\SolvEvent;
use Olz\Entity\SolvPerson;
use Olz\Entity\SolvResult;
use Olz\Fetchers\SolvFetcher;
use Olz\Tests\Fake;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\AbstractDateUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FakeSyncSolvCommandIntegrationTestSolvFetcher extends Fake\FakeFetcher {
    public function fetchEventsCsvForYear($year) {
        return $this->getMockedResponse(
            "solv_events_{$year}", __DIR__,
            function () use ($year) {
                $real_fetcher = new SolvFetcher();
                return $real_fetcher->fetchEventsCsvForYear($year);
            }
        );
    }

    public function fetchYearlyResultsJson($year) {
        return $this->getMockedResponse(
            "solv_yearly_results_{$year}", __DIR__,
            function () use ($year) {
                $real_fetcher = new SolvFetcher();
                return $real_fetcher->fetchYearlyResultsJson($year);
            }
        );
    }

    public function fetchEventResultsHtml($rank_id) {
        return $this->getMockedResponse(
            "solv_results_html_{$rank_id}", __DIR__,
            function () use ($rank_id) {
                $real_fetcher = new SolvFetcher();
                return $real_fetcher->fetchEventResultsHtml($rank_id);
            }
        );
    }
}

/**
 * @internal
 *
 * @covers \Olz\Command\SyncSolvCommand
 */
final class SyncSolvCommandIntegrationTest extends IntegrationTestCase {
    public function testRun(): void {
        $this->withLockedDb(function () {
            $job = new SyncSolvCommand();
            $env_utils = EnvUtils::fromEnv();
            $date_utils = AbstractDateUtils::fromEnv();
            $logger = Fake\FakeLogger::create();
            $job->setSolvFetcher(new FakeSyncSolvCommandIntegrationTestSolvFetcher());
            $job->setLog($logger);
            $input = new ArrayInput([]);
            $output = new BufferedOutput();

            $result = $job->run($input, $output);

            $this->assertSame(Command::SUCCESS, $result);

            $db_utils = DbUtils::fromEnv();
            $entity_manager = $db_utils->getEntityManager();
            $solv_event_repo = $entity_manager->getRepository(SolvEvent::class);
            $all_events = $solv_event_repo->findAll();
            $solv_person_repo = $entity_manager->getRepository(SolvPerson::class);
            $all_persons = $solv_person_repo->findAll();
            $solv_result_repo = $entity_manager->getRepository(SolvResult::class);
            $all_results = $solv_result_repo->findAll();

            $this->assertSame([
                '6. Nationaler OL 🥶',
                '59. Schweizer 5er Staffel',
                'Zimmerberg Berg-OL',
                '13. Zimmerberg-OL / JOM-Schlusslauf',
                '4. Milchsuppe-Abend OL 2020',
            ], array_map(function ($event) {
                return $event->getName();
            }, $all_events));

            $this->assertSame([
                'Toni 😁 Thalwiler',
                'Hanna Horgener',
                'Walter Wädenswiler',
                'Regula Richterswiler',
                'Benjamin Klieber',
                'Bigna Hotz',
                'Sanjana Klieber',
                'Heidi Gross',
                'Estere Rasscevska',
                'Curdin Hotz',
                'Amylou Scanzi',
                'Joshua Hückstädt',
                'Finlay Scanzi',
                'Maria Breitenmoser',
                'Rilli Scanzi',
                'Leeroy Scanzi',
                'Jonathan Kühne',
                'Valentin Kühne',
                'Esther Gasser',
                'Andrea Klieber',
                'Roland Böhi',
                'Livia Auf der Mauer',
                'Tim Attinger',
                'Sara Rüegg',
                'Gian Rettich',
                'Liliane Suter',
                'Manuel Gasser',
                'Anik Bachmann',
                'Martin Gross',
                'Lena Gasser',
                'Andrina Hotz',
                'Sophia Bernasconi',
                'Mischa Bachmann',
                'Thomas Attinger',
                'Katrin Rettich',
                'Roger Fluri',
                'Flurin Rettich',
                'Anna Rettich',
                'Regina Neukom',
                'Zigmars Rasscevskis',
                'Martin Attinger',
                'Aurelius Kasper',
                'Mario Hiestand',
                'Otti Bisang',
                'Anita Gasser',
                'Bernadette Huber',
                'Patrizia Köpfli',
                'Priska Badertscher',
                'Peter Laager',
                'Aaron Hiestand',
                'Madara Rasscevska',
                'Sophie Attinger',
                'Katharina Attinger',
                'Marlies Laager',
                'Rico Huber',
                'Romeo Böhi',
                'Denis Fuger',
                'Florian Attinger',
                'Julia Jakob',
                'Markus Hotz',
                'Gratian Böhi',
                'Raphael Neukom',
                'Michael Felder',
                'Michael Laager',
                'Michael Gasser',
                'Silvan Ghirlanda',
                'Simon Hatt',
                'Marc Breitenmoser',
                'Lilly Gross',
                'Chris Seitz',
                'Giulia Borner',
                'Dominik Badertscher',
                'Sergio Zanelli',
                'Jonas Junker',
                'Sandro Auf der Mauer',
                'Marc Bitterli',
                'Marco Breitenmoser',
                'Philipp Tschannen',
                'Lukas Gasser',
                'Jan Waldmann',
                'Martin R. Attinger',
                'Urs Utzinger',
                'Roland, TimMiro Füssin',
                'Jonas Junker',
                'Michael Koller',
                'Tim Bachmann',
                'Yann Häusler',
                'Luin Dörfler',
                'Max Bill',
                'Max Hagedorn',
                'Elias Holenstein',
                'Serafina Hatt',
                'Tiziana Rigamonti-Amma',
                'Andrea Klieber-Kühne',
                'Andrea Holenstein',
                'Arlette Piguet',
                'Lea Rettich',
                'Martha Haschenburger',
                'Edu Hatt',
                'Arvo Ziegler',
                'Leeroy Rocky Scanzi',
                'Miriam Holensten',
                'Jonas Holenstein',
                'Finley Scanzi',
                'Gian, Curdin Dütschler',
                'Amylou Scanzi',
                'Flurin, Adrian Althaus',
            ], array_map(function ($person) {
                return $person->getName();
            }, $all_persons));

            $this->assertSame([
                '1 6822 HAL 1234 12',
                '2 6822 DAM 4321 43',
                '3 6822 HAK 4231 32',
                '1 7411 HAL 1234 12',
                '3 7411 HAK 4231 32',
                '4 7411 DAK 4321 43',
                '5 10241 Gruen 1189 29',
                '6 10241 Gruen 1310 27',
                '7 10241 Gruen 1623 37',
                '8 10241 Gruen 1641 52',
                '9 10241 Gruen 1826 30',
                '10 10241 Gruen 1911 40',
                '11 10241 Gruen 2148 52',
                '12 10241 Gruen 2375 38',
                '13 10241 Gruen 2443 39',
                '14 10241 Gruen 2536 41',
                '15 10241 Gruen 2581 53',
                '16 10241 Gruen 2655 56',
                '17 10241 Gruen 3122 48',
                '18 10241 Gruen 3152 59',
                '19 10241 Gruen 4265 1824',
                '20 10241 Blau 2758 69',
                '21 10241 Blau 3070 43',
                '22 10241 Blau 3443 36',
                '23 10241 Hellrot 2378 32',
                '24 10241 Hellrot 2518 45',
                '25 10241 Hellrot 2652 34',
                '26 10241 Hellrot 2737 35',
                '27 10241 Hellrot 2836 37',
                '28 10241 Hellrot 2861 37',
                '29 10241 Hellrot 2917 46',
                '30 10241 Hellrot 3087 33',
                '31 10241 Hellrot 3107 36',
                '32 10241 Hellrot 3160 38',
                '33 10241 Hellrot 3251 28',
                '34 10241 Hellrot 3282 48',
                '35 10241 Hellrot 3307 36',
                '36 10241 Hellrot 3370 27',
                '37 10241 Hellrot 3399 31',
                '38 10241 Hellrot 3430 42',
                '39 10241 Hellrot 3482 64',
                '40 10241 Hellrot 3505 34',
                '41 10241 Hellrot 3505 58',
                '42 10241 Hellrot 3556 36',
                '43 10241 Hellrot 3593 35',
                '44 10241 Hellrot 3613 49',
                '45 10241 Hellrot 3803 41',
                '46 10241 Hellrot 3819 48',
                '47 10241 Hellrot 3826 36',
                '48 10241 Hellrot 4082 51',
                '49 10241 Hellrot 4289 37',
                '50 10241 Hellrot 4435 26',
                '51 10241 Hellrot 4647 53',
                '52 10241 Hellrot 5164 41',
                '53 10241 Hellrot 5262 72',
                '54 10241 Hellrot 5507 62',
                '55 10241 Hellrot 6664 44',
                '56 10241 Hellrot -1 36',
                '57 10241 Hellrot -1 41',
                '58 10241 Schwarz 3559 27',
                '59 10241 Schwarz 4107 33',
                '60 10241 Schwarz 4324 32',
                '61 10241 Schwarz 4351 27',
                '62 10241 Schwarz 4530 28',
                '63 10241 Schwarz 4591 20',
                '64 10241 Schwarz 4873 22',
                '65 10241 Schwarz 5011 27',
                '66 10241 Schwarz 5039 23',
                '67 10241 Schwarz 5316 27',
                '68 10241 Schwarz 5320 20',
                '69 10241 Schwarz 5369 32',
                '70 10241 Schwarz 6205 24',
                '71 10241 Schwarz 6332 27',
                '72 10241 Schwarz 6338 26',
                '73 10241 Schwarz 6423 43',
                '74 10241 Schwarz 6445 42',
                '75 10241 Schwarz 6554 39',
                '76 10241 Schwarz 6785 49',
                '77 10241 Schwarz 7253 34',
                '78 10241 Schwarz 7313 25',
                '79 10241 Schwarz -1 23',
                '80 10241 Schwarz -1 34',
                '81 10241 Schwarz -1 87',
                '82 10241 Schwarz -1 73',
                '83 10563 C 3416 20',
                '78 10317 HAL 6427 23',
                '84 10317 HAM -1 35',
                '34 10317 H55 5001 35',
                '29 10317 H60 5512 34',
                '85 10317 H18 4198 21',
                '66 10317 H16 3272 23',
                '86 10317 H16 4736 21',
                '79 10317 H14 1707 20',
                '61 10317 H14 2059 23',
                '25 10317 H14 2319 25',
                '37 10317 H14 2422 25',
                '42 10317 H14 2986 27',
                '50 10317 H14 3682 24',
                '87 10317 H14 5191 26',
                '27 10317 H12 1202 23',
                '56 10317 H12 1421 28',
                '5 10317 H12 1782 43',
                '88 10317 H12 2327 27',
                '89 10317 H12 2558 31',
                '90 10317 H10 1121 26',
                '91 10317 H10 2896 33',
                '92 10317 DAM 4196 28',
                '93 10317 DAK 4078 35',
                '94 10317 DAK 4435 48',
                '95 10317 DB 4150 33',
                '96 10317 D60 3368 29',
                '97 10317 D18 3971 25',
                '30 10317 D18 4134 25',
                '38 10317 D16 3197 25',
                '31 10317 D14 2357 22',
                '7 10317 D14 2425 27',
                '6 10317 D12 1455 22',
                '98 10317 D12 4023 27',
                '99 10317 OL 4910 37',
                '100 10317 OK 1756 30',
                '12 10317 OK 1770 30',
                '101 10317 OK 2440 48',
                '102 10317 OS 2589 36',
                '103 10317 OS 3785 43',
                '104 10317 FAM 1831 24',
                '105 10317 FAM 2780 75',
                '106 10317 FAM 2884 41',
                '107 10317 FAM 3433 56',
            ], array_map(function ($result) {
                $person = $result->getPerson();
                $event = $result->getEvent();
                $class = $result->getClass();
                $time = $result->getResult();
                $finish_split = $result->getFinishSplit();
                return "{$person} {$event} {$class} {$time} {$finish_split}";
            }, $all_results));
        });
    }
}
