<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Suche\Utils;

use Olz\Suche\Utils\SearchUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Suche\Utils\SearchUtils
 */
final class SearchUtilsTest extends UnitTestCase {
    public function testGetCutout(): void {
        $utils = new SearchUtils();

        $this->assertSame('', $utils->getCutout('', []));
        $this->assertSame('test', $utils->getCutout('test', []));
        $this->assertSame('test', $utils->getCutout('test', ['test']));

        $sentence = 'The quick brown fox jumps over the lazy dog';
        $this->assertSame($sentence, $utils->getCutout($sentence, []));
        $this->assertSame($sentence, $utils->getCutout($sentence, ['fox', 'dog']));
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
        $this->assertSame("{$start_tag}test{$end_tag}{$start_tag}test{$end_tag}", $utils->highlight('testtest', ['test']));
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

        // Start/end token escaping
        $this->assertSame("\\[{$start_tag}test{$end_tag}\\]", $utils->highlight('\[test\]', ['test']));
        $this->assertSame("{$start_tag}\\[{$end_tag}{$start_tag}test{$end_tag}{$start_tag}\\]{$end_tag}", $utils->highlight('\[test\]', ['test', '\[', '\]']));
        $this->assertSame("\\[{$start_tag}test{$end_tag}\\]", $utils->highlight('\[test\]', ['test', '[', ']']));
        $this->assertSame("\\\\[{$start_tag}test{$end_tag}\\\\]", $utils->highlight('\\\[test\\\]', ['test']));
        $this->assertSame("{$start_tag}\\\\[{$end_tag}{$start_tag}test{$end_tag}{$start_tag}\\\\]{$end_tag}", $utils->highlight('\\\[test\\\]', ['test', '\\\[', '\\\]']));
        $this->assertSame("\\\\[{$start_tag}test{$end_tag}\\\\]", $utils->highlight('\\\[test\\\]', ['test', '\[', '\]']));

        // Not desired, but reflect how the current implementation works:
        $this->assertSame("{$start_tag}{$start_tag}test{$end_tag}{$end_tag} {$start_tag}{$start_tag}test{$end_tag}{$end_tag}", $utils->highlight('test test', ['test', 'test']));
        $this->assertSame("{$start_tag}t{$start_tag}es{$end_tag}t{$end_tag} {$start_tag}t{$start_tag}es{$end_tag}t{$end_tag}", $utils->highlight('test test', ['test', 'es']));
        $this->assertSame("{$start_tag}tes{$end_tag}t {$start_tag}tes{$end_tag}t", $utils->highlight('test test', ['tes', 'est']));

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
}
