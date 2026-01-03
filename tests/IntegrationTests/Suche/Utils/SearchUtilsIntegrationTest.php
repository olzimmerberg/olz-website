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
            ['title' => 'News', 'bestScore' => 0.82764, 'results' => [
                [
                    'link' => '/news/1203',
                    'icon' => '/assets/icns/entry_type_video_20.svg',
                    'date' => new \DateTime('2020-08-13'),
                    'title' => 'Test Video',
                    'text' => null,
                    'score' => 0.82764,
                    'debug' => 'Score: 0.82764 / Time relevance: 1',
                ],
                [
                    'link' => '/news/7',
                    'icon' => '/assets/icns/entry_type_video_20.svg',
                    'date' => new \DateTime('2020-08-15'),
                    'title' => 'Test Video',
                    'text' => 'https://youtu.be/JVL0vgcnM6c',
                    'score' => 0.78051,
                    'debug' => 'Score: 0.78051 / Time relevance: 1',
                ],
                [
                    'link' => '/news/3',
                    'icon' => '/assets/icns/entry_type_aktuell_20.svg',
                    'date' => new \DateTime('2020-01-01'),
                    'title' => 'Frohes neues Jahr! 🎆',
                    'text' => '…Bisschen.* Zumindest so weit, dass das auf der Testseite irgendwie einigermassen gut aussieht. Und…',
                    'score' => 0.07422,
                    'debug' => 'Score: 0.07422 / Time relevance: 0.619',
                ],
            ]],
            ['title' => 'Fragen & Antworten', 'bestScore' => 0.29044, 'results' => [
                [
                    'link' => '/fragen_und_antworten/5',
                    'icon' => '/assets/icns/question_mark_20.svg',
                    'date' => null,
                    'title' => 'Wie reise ich zu einem Training?',
                    'text' => "…gs-Büssli.\n\nWenn du mit dem Büssli anreisen möchtest, melde dich bitte im Voraus beim [Büsslikoordin…",
                    'score' => 0.29044,
                    'debug' => 'Score: 0.29044 / Time relevance: 1',
                ],
                [
                    'link' => '/fragen_und_antworten/14',
                    'icon' => '/assets/icns/question_mark_20.svg',
                    'date' => null,
                    'title' => 'Wie finde ich meinen Benutzernamen bzw. E-Mail heraus?',
                    'text' => '…angelangt bist, bleibt leider nur noch raten, welche E-Mail Adresse du verwendet haben könntest.',
                    'score' => 0.25098,
                    'debug' => 'Score: 0.25098 / Time relevance: 1',
                ],
            ]],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Karten', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Ressorts', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Service', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Suche',  'bestScore' => null, 'results' => []],
            ['title' => 'Termine', 'bestScore' => null, 'results' => []],
            ['title' => 'Termin-Listen', 'bestScore' => null, 'results' => []],
            ['title' => 'Termin-Orte', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
        ], $this->getSut()->getSearchResults(['test']));
    }

    protected function getSut(): SearchUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(SearchUtils::class);
    }
}
