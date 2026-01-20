<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Suche\Utils;

use Olz\Suche\Utils\SearchUtils;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Suche\Utils\SearchUtils
 */
final class SearchUtilsIntegrationTest extends IntegrationTestCase {
    public function testGetSearchResultsForTest(): void {
        $this->assertEquals([
            [
                'link' => '/news/1203',
                'icon' => '/assets/icns/entry_type_video_20.svg',
                'date' => new \DateTime('2020-08-13'),
                'title' => 'News (Videos): Test Video',
                'text' => null,
                'score' => 0.72975,
                'debug' => 'Score: 0.72975 / Time relevance: 1',
            ],
            [
                'link' => '/news/7',
                'icon' => '/assets/icns/entry_type_video_20.svg',
                'date' => new \DateTime('2020-08-15'),
                'title' => 'News (Videos): Test Video',
                'text' => 'https://youtu.be/JVL0vgcnM6c',
                'score' => 0.69881,
                'debug' => 'Score: 0.69881 / Time relevance: 1',
            ],
            [
                'link' => '/fragen_und_antworten/trainings_anreise',
                'icon' => '/assets/icns/question_mark_20.svg',
                'date' => null,
                'title' => 'Frage: Wie reise ich zu einem Training?',
                'text' => "…gs-Büssli.\n\nWenn du mit dem Büssli anreisen möchtest, melde dich bitte im Voraus beim [Büsslikoordin…",
                'score' => 0.28271,
                'debug' => 'Score: 0.28271 / Time relevance: 1',
            ],
            [
                'link' => '/fragen_und_antworten/benutzername_email_herausfinden',
                'icon' => '/assets/icns/question_mark_20.svg',
                'date' => null,
                'title' => 'Frage: Wie finde ich meinen Benutzernamen bzw. E-Mail heraus?',
                'text' => '…angelangt bist, bleibt leider nur noch raten, welche E-Mail Adresse du verwendet haben könntest.',
                'score' => 0.24619,
                'debug' => 'Score: 0.24619 / Time relevance: 1',
            ],
            [
                'link' => '/news/3',
                'icon' => '/assets/icns/entry_type_aktuell_20.svg',
                'date' => new \DateTime('2020-01-01'),
                'title' => 'News (Aktuell): Frohes neues Jahr! 🎆',
                'text' => '…Bisschen.* Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und…',
                'score' => 0.10221,
                'debug' => 'Score: 0.10221 / Time relevance: 0.873',
            ],
        ], $this->getSut()->getSearchResults(['test']));
    }

    protected function getSut(): SearchUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SearchUtils::class);
    }
}
