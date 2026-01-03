<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Suche\Utils;

use Doctrine\Common\Collections\Criteria;
use Olz\Suche\Utils\SearchUtils;
use Olz\Tests\Fake\FakeSearchUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Suche\Utils\SearchUtils
 */
final class SearchUtilsTest extends UnitTestCase {
    public function testGetSearchResults(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => false];
        $utils = new FakeSearchUtils();
        $this->assertSame([
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
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Suche', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'Termin-Listen', 'bestScore' => null, 'results' => []],
            ['title' => 'Termin-Orte', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
        ], $utils->getSearchResults(['test']));
    }

    public function testGetSearchResultsAdmin(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['termine' => true];
        $utils = new FakeSearchUtils();
        $this->assertSame([
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
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'Suche', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'Termin-Listen', 'bestScore' => null, 'results' => []],
            ['title' => 'Fake title', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
            ['title' => 'TODO', 'bestScore' => null, 'results' => []],
        ], $utils->getSearchResults(['test']));
    }

    public function testGetDateCriteria(): void {
        $utils = new SearchUtils();
        $this->assertSame([], $utils->getDateCriteria('field', 'test'));
        $this->assertEquals([Criteria::expr()->andX(
            Criteria::expr()->gte('field', new \DateTime('2020-01-01')),
            Criteria::expr()->lt('field', new \DateTime('2021-01-01')),
        )], $utils->getDateCriteria('field', '2020'));
    }

    public function testGetStaticSearchResults(): void {
        $utils = new SearchUtils();
        $this->assertSame([[
            'score' => 0.85714,
            'icon' => null,
            'date' => null,
            'text' => 'Test this test',
            'link' => '/',
            'title' => 'Title',
        ]], $utils->getStaticSearchResults('Test this test', ['test'], ['link' => '/', 'title' => 'Title']));
        $this->assertEquals([], $utils->getStaticSearchResults('test', ['foo', 'bar'], ['link' => '/', 'title' => 'Title']));
    }

    public function testGetScoredSearchResult(): void {
        $utils = new SearchUtils();
        $this->assertEquals(
            [
                'icon' => null,
                'date' => null,
                'link' => '/',
                'title' => 'Title',
                'text' => 'Test this test',
                'score' => 0.85714,
            ],
            $utils->getScoredSearchResult(['link' => '/', 'title' => 'Title', 'text' => 'Test this test'], ['test']),
        );
        // Title matches count double
        $this->assertEquals(
            [
                'icon' => null,
                'date' => null,
                'link' => '/',
                'title' => 'Title',
                'text' => 'Test this test',
                'score' => 0.85714,
            ],
            $utils->getScoredSearchResult(['link' => '/', 'title' => 'Title', 'text' => 'Test this test'], ['title']),
        );
        $this->assertEquals(
            [
                'link' => '/',
                'icon' => null,
                'date' => null,
                'title' => 'Title',
                'text' => 'test',
                'score' => 0.0,
            ],
            $utils->getScoredSearchResult(['link' => '/', 'title' => 'Title', 'text' => 'test'], ['foo', 'bar']),
        );
    }

    public function testGetDateFormattings(): void {
        $utils = new SearchUtils();
        $this->assertSame([], $utils->getDateFormattings(null));
        $this->assertSame(
            ['2020-03-13', '13.03.2020', '13.3.2020'],
            $utils->getDateFormattings(new \DateTime('2020-03-13')),
        );
    }

    public function testAnalyze(): void {
        $utils = new SearchUtils();
        $this->assertSame(
            ['score' => 0.85714, 'hasAll' => true],
            $utils->analyze('Test this test', null, ['test']),
        );
        $this->assertEquals(
            ['score' => 0.5, 'hasAll' => true],
            $utils->analyze('Test this test', new \DateTime('2020-03-13'), ['2020']),
        );
    }

    public function testAnalyzeNonMatch(): void {
        $utils = new SearchUtils();
        $this->assertSame(
            ['score' => 0.0, 'hasAll' => false],
            $utils->analyze('sägen', null, ['sagen']),
        );
    }

    public function testAnalyzeWordMatch(): void {
        $utils = new SearchUtils();
        $this->assertSame(
            ['score' => 0.75, 'hasAll' => true],
            $utils->analyze('A word match', null, ['word']),
        );
        $this->assertSame(
            ['score' => 0.66667, 'hasAll' => true],
            $utils->analyze('A wordstart match', null, ['word']),
        );
        $this->assertSame(
            ['score' => 0.66667, 'hasAll' => true],
            $utils->analyze('A endword match', null, ['word']),
        );
        $this->assertSame(
            ['score' => 0.5, 'hasAll' => true],
            $utils->analyze('A nonwordstartend match', null, ['word']),
        );

        // Start / End of content
        $this->assertSame(
            ['score' => 0.75, 'hasAll' => true],
            $utils->analyze('word', null, ['word']),
        );
        $this->assertSame(
            ['score' => 0.66667, 'hasAll' => true],
            $utils->analyze('wordstart', null, ['word']),
        );
        $this->assertSame(
            ['score' => 0.66667, 'hasAll' => true],
            $utils->analyze('endword', null, ['word']),
        );
        $this->assertSame(
            ['score' => 0.5, 'hasAll' => true],
            $utils->analyze('nonwordstartend', null, ['word']),
        );
    }

    public function testAnalyzeTermCombinations(): void {
        $utils = new SearchUtils();
        // 2-term combined match, space separated (base case)
        $this->assertSame(
            ['score' => 0.84615, 'hasAll' => true],
            $utils->analyze('Test this test', null, ['test', 'this']),
        );
        // 2-term combined match, non-alphanumeric separated
        $this->assertSame(
            ['score' => 0.84615, 'hasAll' => true],
            $utils->analyze('Test -> this test', null, ['test', 'this']),
        );
        // 2-term combined match, 5-non-alphanumeric separated
        $this->assertSame(
            ['score' => 0.84615, 'hasAll' => true],
            $utils->analyze('Test --- this test', null, ['test', 'this']),
        );
        // 2-term combined match, 6-non-alphanumeric separated
        $this->assertSame(
            ['score' => 0.81818, 'hasAll' => true],
            $utils->analyze('Test ---- this test', null, ['test', 'this']),
        );
        // 2-term combined match, multi-whitespace separated
        $this->assertSame(
            ['score' => 0.84615, 'hasAll' => true],
            $utils->analyze("Test \n\t    this test", null, ['test', 'this']),
        );
        // No combined match, alphanumeric-separated
        $this->assertSame(
            ['score' => 0.81818, 'hasAll' => true],
            $utils->analyze('Test some of this test', null, ['test', 'this']),
        );

        $sentence = 'The quick brown fox jumps over the lazy dog';
        // 3-term combined match, space separated (base case)
        $this->assertSame(
            ['score' => 0.84211, 'hasAll' => true],
            $utils->analyze($sentence, null, ['quick', 'brown', 'fox']),
        );
        // 4-term combined match, space separated (base case)
        $this->assertSame(
            ['score' => 0.875, 'hasAll' => true],
            $utils->analyze($sentence, null, ['quick', 'brown', 'fox', 'jumps']),
        );
        // 5-term combined match, space separated (base case)
        $this->assertSame(
            ['score' => 0.88889, 'hasAll' => true],
            $utils->analyze($sentence, null, ['quick', 'brown', 'fox', 'jumps', 'over']),
        );
        // 6-term combined match, space separated (base case)
        $this->assertSame(
            ['score' => 0.90164, 'hasAll' => true],
            $utils->analyze($sentence, null, ['the', 'quick', 'brown', 'fox', 'jumps', 'over']),
        );
        $this->assertSame(
            ['score' => 0.90164, 'hasAll' => true],
            $utils->analyze($sentence, null, ['quick', 'brown', 'fox', 'jumps', 'over', 'the']),
        );
        // 7-term combined match, space separated (base case)
        $this->assertSame(
            ['score' => 0.90909, 'hasAll' => true],
            $utils->analyze($sentence, null, ['the', 'quick', 'brown', 'fox', 'jumps', 'over', 'the']),
        );
    }

    public function testGetCutout(): void {
        $utils = new SearchUtils();

        $this->assertSame('', $utils->getCutout('', []));
        $this->assertSame('test', $utils->getCutout('test', []));
        $this->assertSame('', $utils->getCutout('', ['test']));
        $this->assertSame('test', $utils->getCutout('test', ['test']));

        $this->assertSame(
            'Just test this Test, man!',
            $utils->getCutout('Just test this Test, man!', ['test'])
        );
        $this->assertSame(
            '…test…',
            $utils->getCutout('Just test this Test, man!', ['test'], 4)
        );
        $this->assertSame(
            '…test…', // white space is trimmed
            $utils->getCutout('Just test this Test, man!', ['test'], 6)
        );
        $this->assertSame(
            '…test t…',
            $utils->getCutout('Just test this Test, man!', ['test'], 7)
        );
        $this->assertSame(
            '…t test t…',
            $utils->getCutout('Just test this Test, man!', ['test'], 8)
        );
        $this->assertSame(
            '…test this Test…',
            $utils->getCutout('Just test this Test, man!', ['test'], 14)
        );
        $this->assertSame(
            '…test this Test,…', // white space is trimmed
            $utils->getCutout('Just test this Test, man!', ['test'], 16)
        );
        $this->assertSame(
            '…t test this Test,…', // white space is trimmed
            $utils->getCutout('Just test this Test, man!', ['test'], 18)
        );
        $this->assertSame(
            '…st test this Test, m…', // white space is trimmed
            $utils->getCutout('Just test this Test, man!', ['test'], 20)
        );

        $sentence = 'The quick brown fox jumps over the lazy dog';
        $this->assertSame($sentence, $utils->getCutout($sentence, []));
        $this->assertSame($sentence, $utils->getCutout($sentence, ['fox', 'brown']));
        $this->assertSame('…fox…', $utils->getCutout($sentence, ['fox', 'brown'], 3));
        $this->assertSame('…brown…', $utils->getCutout($sentence, ['fox', 'brown'], 5));
        $this->assertSame('…brown fox…', $utils->getCutout($sentence, ['fox', 'brown'], 9));
        $this->assertSame('…ck brown fox ju…', $utils->getCutout($sentence, ['fox', 'brown'], 15));
    }

    public function testGetCutoutMultibyte(): void {
        $utils = new SearchUtils();

        $sentence = 'Äfach dä Test ämal teste, Löli!';
        $this->assertSame($sentence, $utils->getCutout($sentence, []));
        $this->assertSame($sentence, $utils->getCutout($sentence, ['test', 'dä']));
        $this->assertSame('…dä…', $utils->getCutout($sentence, ['test', 'dä'], 2));
        $this->assertSame('…Test…', $utils->getCutout($sentence, ['test', 'dä'], 4));
        $this->assertSame('…dä Test…', $utils->getCutout($sentence, ['test', 'dä'], 7));
        $this->assertSame('…ch dä Test äm…', $utils->getCutout($sentence, ['test', 'dä'], 13));
    }

    public function testGetOffsets(): void {
        $utils = new SearchUtils();

        $this->assertSame([], $utils->getOffsets('', []));
        $this->assertSame([], $utils->getOffsets('test', []));
        $this->assertSame([[]], $utils->getOffsets('', ['test']));
        $this->assertSame([[0]], $utils->getOffsets('test', ['test']));
        $this->assertSame([[0, 4]], $utils->getOffsets('TesttesT', ['test']));

        $this->assertSame([[0, 3], [1, 2]], $utils->getOffsets('ABBA', ['a', 'b']));
        $this->assertSame([[0, 3], [0]], $utils->getOffsets('ABBA', ['a', 'ab']));
        $this->assertSame([[0, 3], [2]], $utils->getOffsets('ABBA', ['a', 'ba']));
        $this->assertSame([[], [1]], $utils->getOffsets('ABBA', ['aa', 'bb']));

        // Regex delimiter
        $this->assertSame([[2]], $utils->getOffsets('AC/DC', ['/']));

        // Multibyte characters
        $this->assertSame([[1, 4], [2, 5]], $utils->getOffsets('ÄÖÜäöÜ', ['ö', 'ü']));
    }

    public function testCensorEmails(): void {
        $utils = new SearchUtils();

        $this->assertSame('', $utils->censorEmails(''));
        $this->assertSame('***@***', $utils->censorEmails('e.mail+test@other-domain.com'));
        $this->assertSame('***@***', $utils->censorEmails('vorstand_role@staging.olzimmerberg.ch'));
        $this->assertSame('***@***', $utils->censorEmails('inexistent@staging.olzimmerberg.ch'));
        $this->assertSame('***@***', $utils->censorEmails('vorstand@staging.olzimmerberg.ch'));
        $this->assertSame('E-Mail:***@*** Weiter', $utils->censorEmails('E-Mail:e.mail@other-domain.com. Weiter'));
    }

    public function testHighlight(): void {
        $utils = new SearchUtils();
        $start_tag = '<span class="highlight">';
        $end_tag = '</span>';

        $this->assertSame('', $utils->highlight('', []));
        $this->assertSame('test', $utils->highlight('test', []));
        $this->assertSame("{$start_tag}test{$end_tag}", $utils->highlight('test', ['test']));
        $this->assertSame("{$start_tag}test{$end_tag}1234", $utils->highlight('test1234', ['test']));

        // Repetition
        $this->assertSame("{$start_tag}testtest{$end_tag}", $utils->highlight('testtest', ['test']));
        $this->assertSame("{$start_tag}test{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('test test', ['test']));

        // Capitalization
        $this->assertSame("{$start_tag}TEST{$end_tag} {$start_tag}Test{$end_tag} {$start_tag}TeSt{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('TEST Test TeSt test', ['TEST']));
        $this->assertSame("{$start_tag}TEST{$end_tag} {$start_tag}Test{$end_tag} {$start_tag}TeSt{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('TEST Test TeSt test', ['Test']));
        $this->assertSame("{$start_tag}TEST{$end_tag} {$start_tag}Test{$end_tag} {$start_tag}TeSt{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('TEST Test TeSt test', ['TeSt']));
        $this->assertSame("{$start_tag}TEST{$end_tag} {$start_tag}Test{$end_tag} {$start_tag}TeSt{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('TEST Test TeSt test', ['test']));

        // Regex delimiter
        $this->assertSame("{$start_tag}test{$end_tag}", $utils->highlight('test', ['test', '/']));

        // Start/end tag substring replacement
        $this->assertSame("{$start_tag}test{$end_tag}", $utils->highlight('test', ['test', 'span']));

        // Overlapping matches
        $this->assertSame("{$start_tag}test{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('test test', ['test', 'test']));
        $this->assertSame("{$start_tag}test{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('test test', ['test', 'es']));
        $this->assertSame("{$start_tag}test{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('test test', ['tes', 'est']));
        $this->assertSame("{$start_tag}test{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('test test', ['test', 'tes']));
        $this->assertSame("{$start_tag}test{$end_tag} {$start_tag}test{$end_tag}", $utils->highlight('test test', ['test', 'est']));

        // Use case:
        $this->assertSame(
            "The quick brown fox jumps over the lazy dog",
            $utils->highlight('The quick brown fox jumps over the lazy dog', []),
        );
        $this->assertSame(
            "The quick brown {$start_tag}fox{$end_tag} jumps over the lazy {$start_tag}dog{$end_tag}",
            $utils->highlight(
                'The quick brown fox jumps over the lazy dog',
                ['fox', 'dog'],
            ),
        );
        $this->assertSame(
            "{$start_tag}The{$end_tag} quick brown fox jumps over {$start_tag}the{$end_tag} lazy dog",
            $utils->highlight('The quick brown fox jumps over the lazy dog', ['the']),
        );
        $this->assertSame(
            "{$start_tag}The{$end_tag} quick brown fox jumps over {$start_tag}the{$end_tag} lazy dog",
            $utils->highlight('The quick brown fox jumps over the lazy dog', ['The']),
        );
        $this->assertSame(
            "{$start_tag}The{$end_tag} quick brown fox jumps over {$start_tag}the{$end_tag} lazy dog",
            $utils->highlight('The quick brown fox jumps over the lazy dog', ['tHe']),
        );
    }

    public function testNormalizeRanges(): void {
        $utils = new SearchUtils();

        $this->assertSame([], $utils->normalizeRanges([]));
        $this->assertSame([[0, 0]], $utils->normalizeRanges([[0, 0]]));
        $this->assertSame([[0, 1]], $utils->normalizeRanges([[0, 1]]));
        $this->assertSame([[0, 1], [2, 3]], $utils->normalizeRanges([[0, 1], [2, 3]]));
        $this->assertSame([[0, 3]], $utils->normalizeRanges([[0, 2], [1, 3]]));
        $this->assertSame([[0, 3]], $utils->normalizeRanges([[0, 3], [1, 2]]));
        $this->assertSame([[0, 1], [2, 3], [4, 5]], $utils->normalizeRanges([[0, 1], [2, 3], [4, 5]]));
        $this->assertSame([[0, 3], [4, 5]], $utils->normalizeRanges([[0, 3], [1, 2], [4, 5]]));
        $this->assertSame([[0, 1], [2, 5]], $utils->normalizeRanges([[0, 1], [2, 5], [3, 4]]));
        $this->assertSame([[0, 7]], $utils->normalizeRanges([[0, 3], [2, 5], [4, 7]]));
        $this->assertSame([[0, 4]], $utils->normalizeRanges([[0, 2], [1, 3], [2, 4]]));
        $this->assertSame([[0, 3]], $utils->normalizeRanges([[0, 1], [1, 2], [2, 3]]));
        $this->assertSame([[0, 5]], $utils->normalizeRanges([[0, 5], [1, 4], [2, 3]]));
        $this->assertSame([[0, 5]], $utils->normalizeRanges([[2, 3], [1, 4], [0, 5]]));
    }
}
