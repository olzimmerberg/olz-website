<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HtmlUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\HtmlUtils
 */
final class HtmlUtilsTest extends UnitTestCase {
    public function testRenderMarkdown(): void {
        $html_utils = new HtmlUtils();

        // Ignore HTML
        $html = $html_utils->renderMarkdown("Normal<h1>H1</h1><script>alert('not good!');</script>");
        $this->assertSame("<div class='rendered-markdown'><p>Normal&lt;h1&gt;H1&lt;/h1&gt;&lt;script&gt;alert('not good!');&lt;/script&gt;</p>\n</div>", $html);

        // Headings
        $html = $html_utils->renderMarkdown("Normal\n# H1\n## H2\n### H3\nNormal");
        $this->assertSame("<div class='rendered-markdown'><p>Normal</p>\n<h1>H1</h1>\n<h2>H2</h2>\n<h3>H3</h3>\n<p>Normal</p>\n</div>", $html);

        // Font style
        $html = $html_utils->renderMarkdown("Normal **fe\\*\\*tt** __fe\\_\\_tt__");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <strong>fe**tt</strong> <strong>fe__tt</strong></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Normal *kur\\*siv* _kur\\_siv_");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <em>kur*siv</em> <em>kur_siv</em></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Normal ~~durch\\~\\~gestrichen~~");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <del>durch~~gestrichen</del></p>\n</div>", $html);

        // Quotes
        $html = $html_utils->renderMarkdown("Normal\n> quote\nstill quote\n\nnot anymore");
        $this->assertSame("<div class='rendered-markdown'><p>Normal</p>\n<blockquote>\n<p>quote\nstill quote</p>\n</blockquote>\n<p>not anymore</p>\n</div>", $html);

        // Ordered lists
        $html = $html_utils->renderMarkdown("Normal\n1. one\n2. two\n3. three\nstill three\n\nnot anymore");
        $this->assertSame("<div class='rendered-markdown'><p>Normal</p>\n<ol>\n<li>one</li>\n<li>two</li>\n<li>three\nstill three</li>\n</ol>\n<p>not anymore</p>\n</div>", $html);

        // Unordered lists
        $html = $html_utils->renderMarkdown("Normal\n- one\n- two\n- three\nstill three\n\nnot anymore");
        $this->assertSame("<div class='rendered-markdown'><p>Normal</p>\n<ul>\n<li>one</li>\n<li>two</li>\n<li>three\nstill three</li>\n</ul>\n<p>not anymore</p>\n</div>", $html);

        // Code
        $html = $html_utils->renderMarkdown("Normal `co\\`de`");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <code>co\\</code>de`</p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Normal ```co`de```");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <code>co`de</code></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Normal\n```python\nco`de\n```");
        $this->assertSame("<div class='rendered-markdown'><p>Normal</p>\n<pre><code class=\"language-python\">co`de\n</code></pre>\n</div>", $html);

        // Horizontal rule
        $html = $html_utils->renderMarkdown("something\n\n---\n\ndifferent");
        $this->assertSame("<div class='rendered-markdown'><p>something</p>\n<hr />\n<p>different</p>\n</div>", $html);

        // Links
        $html = $html_utils->renderMarkdown("Normal [link](http://127.0.0.1/)");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <a href=\"http://127.0.0.1/\">link</a></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Normal http://127.0.0.1/");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <a href=\"http://127.0.0.1/\">http://127.0.0.1/</a></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Hier:\nhttps://docs.google.com/spreadsheets/d/1234567890abcdefghijklmnopqrstuvwxyzABCDEFGH/edit#gid=0");
        $this->assertSame("<div class='rendered-markdown'><p>Hier:\n<a href=\"https://docs.google.com/spreadsheets/d/1234567890abcdefghijklmnopqrstuvwxyzABCDEFGH/edit#gid=0\">https://docs.google.com/spreadsheets/d/1234567890abcdefghijklmnopqrstuvwxyzABCDEFGH/edit#gid=0</a></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("user+olz@gmail.com");
        $this->assertSame("<div class='rendered-markdown'><p><script>olz.MailTo(\"user+olz\", \"gmail.com\", \"user+olz\" + \"@gmail.com\")</script></p>\n</div>", $html);

        // Image
        $html = $html_utils->renderMarkdown("Normal ![bird](img/bird.jpg)");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <img src=\"img/bird.jpg\" alt=\"bird\" /></p>\n</div>", $html);

        // Table
        $html = $html_utils->renderMarkdown("Normal\n\n| left | middle | right |\n| --- | --- | --- |\n| 1 | 2 | 3 |\n\nafter");
        $this->assertSame("<div class='rendered-markdown'><p>Normal</p>\n<table>\n<thead>\n<tr>\n<th>left</th>\n<th>middle</th>\n<th>right</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td>1</td>\n<td>2</td>\n<td>3</td>\n</tr>\n</tbody>\n</table>\n<p>after</p>\n</div>", $html);

        // Footnote
        $html = $html_utils->renderMarkdown("This. [^1]\n\n[^1]: explains everything\n");
        // does not work
        $this->assertSame("<div class='rendered-markdown'><p>This. [^1]</p>\n<p>[^1]: explains everything</p>\n</div>", $html);

        // Heading ID
        $html = $html_utils->renderMarkdown("# So linkable {#anchor}\n");
        // does not work
        $this->assertSame("<div class='rendered-markdown'><h1 id=\"anchor\">So linkable</h1>\n</div>", $html);

        // Heading ID
        $html = $html_utils->renderMarkdown("- [x] finish\n- [ ] this\n- [ ] list\n");
        $this->assertSame("<div class='rendered-markdown'><ul>\n<li><input checked=\"\" disabled=\"\" type=\"checkbox\"> finish</li>\n<li><input disabled=\"\" type=\"checkbox\"> this</li>\n<li><input disabled=\"\" type=\"checkbox\"> list</li>\n</ul>\n</div>", $html);
    }

    public function testPostprocess(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "E-Mail")</script>',
            $html_utils->postprocess('e.mail+test@staging.olzimmerberg.ch')
        );
    }

    public function testReplacePureEmailAdresses(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "E-Mail")</script>',
            $html_utils->replaceEmailAdresses('e.mail+test@staging.olzimmerberg.ch')
        );
        $this->assertSame(
            'Mail: <script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "E-Mail")</script>.',
            $html_utils->replaceEmailAdresses('Mail: e.mail+test@staging.olzimmerberg.ch.')
        );
        $this->assertSame(
            'Mails: <script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "E-Mail")</script>, <script>olz.MailTo("e.mail", "staging.olzimmerberg.ch", "E-Mail")</script>.',
            $html_utils->replaceEmailAdresses('Mails: e.mail+test@staging.olzimmerberg.ch, e.mail@staging.olzimmerberg.ch.')
        );
    }

    public function testReplaceMailToLinksWithoutSubject(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "Test" + "")</script>',
            $html_utils->replaceEmailAdresses('<a href="mailto:e.mail+test@staging.olzimmerberg.ch">Test</a>')
        );
        $this->assertSame(
            'Mail: <script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "Contact me" + "")</script>!',
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:e.mail+test@staging.olzimmerberg.ch" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            'Mails: <script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "Contact me" + "")</script> <script>olz.MailTo("e.mail", "staging.olzimmerberg.ch", "Contact me" + "")</script>!',
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:e.mail+test@staging.olzimmerberg.ch" class="linkmail">Contact me</a> <a name="" href="mailto:e.mail@staging.olzimmerberg.ch">Contact me</a>!')
        );
    }

    public function testReplaceMailToLinksWithSubject(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '<script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "Test" + "", "test")</script>',
            $html_utils->replaceEmailAdresses('<a href="mailto:e.mail+test@staging.olzimmerberg.ch?subject=test">Test</a>')
        );
        $this->assertSame(
            'Mail: <script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "Contact me" + "", "another%20test")</script>!',
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:e.mail+test@staging.olzimmerberg.ch?subject=another%20test" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            'Mails: <script>olz.MailTo("e.mail+test", "staging.olzimmerberg.ch", "Contact me" + "", "another%20test")</script>, <script>olz.MailTo("e.mail", "staging.olzimmerberg.ch", "Contact me" + "", "another%20test")</script>!',
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:e.mail+test@staging.olzimmerberg.ch?subject=another%20test" class="linkmail">Contact me</a>, <a name="" href="mailto:e.mail@staging.olzimmerberg.ch?subject=another%20test">Contact me</a>!')
        );
    }

    public function testGetImageSrcHtml(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            '',
            $html_utils->getImageSrcHtml([])
        );
        $this->assertSame(
            "src='fake-image.jpg'",
            $html_utils->getImageSrcHtml(['1x' => 'fake-image.jpg'])
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
            srcset='
                fake-image.jpg 1x,
                fake-image@2x.jpg 2x
            '
            src='fake-image.jpg'
            ZZZZZZZZZZ,
            $html_utils->getImageSrcHtml(['1x' => 'fake-image.jpg', '2x' => 'fake-image@2x.jpg'])
        );
    }
}
