<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HtmlUtils;
use Olz\Utils\WithUtilsCache;

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
        $this->assertSame("<div class='rendered-markdown'><p>something</p>\n<hr>\n<p>different</p>\n</div>", $html);

        // Links
        $html = $html_utils->renderMarkdown("Normal [link](http://127.0.0.1/)");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <a href=\"http://127.0.0.1/\">link</a></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Normal http://127.0.0.1/");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <a href=\"http://127.0.0.1/\">http://127.0.0.1/</a></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("Hier:\nhttps://docs.google.com/spreadsheets/d/1234567890abcdefghijklmnopqrstuvwxyzABCDEFGH/edit#gid=0");
        $this->assertSame("<div class='rendered-markdown'><p>Hier:\n<a href=\"https://docs.google.com/spreadsheets/d/1234567890abcdefghijklmnopqrstuvwxyzABCDEFGH/edit#gid=0\">https://docs.google.com/spreadsheets/d/1234567890abcdefghijklmnopqrstuvwxyzABCDEFGH/edit#gid=0</a></p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("user+olz@gmail.com");
        $this->assertSame(<<<'ZZZZZZZZZZ'
            <div class='rendered-markdown'><p><a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJkUnE3QXRKekNKVFhoc1VadlM2SUpnIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFlTi1zaVZPWUYtT05iYU0xUWJMbFM2VTB3MGI0dkRzRGZ2QXRGVjZIeG5qa3ItcFNsUVRDcVp1M2NsS2xHY3E2cmtha1EifQ&quot;)" class="linkmail">
                E-Mail
            </a></p>
            </div>
            ZZZZZZZZZZ, $html);

        // Image
        $html = $html_utils->renderMarkdown("Normal ![bird](img/bird.jpg)");
        $this->assertSame("<div class='rendered-markdown'><p>Normal <img src=\"img/bird.jpg\" alt=\"bird\"></p>\n</div>", $html);

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

        // CSS Class
        $html = $html_utils->renderMarkdown("So classy {.test}\n");
        $this->assertSame("<div class='rendered-markdown'><p class=\"test\">So classy</p>\n</div>", $html);
        $html = $html_utils->renderMarkdown("# So classy {.test}\n");
        $this->assertSame("<div class='rendered-markdown'><h1 class=\"test\">So classy</h1>\n</div>", $html);

        // Checkbox list
        $html = $html_utils->renderMarkdown("- [x] finish\n- [ ] this\n- [ ] list\n");
        $this->assertSame("<div class='rendered-markdown'><ul>\n<li><input checked=\"\" disabled=\"\" type=\"checkbox\"> finish</li>\n<li><input disabled=\"\" type=\"checkbox\"> this</li>\n<li><input disabled=\"\" type=\"checkbox\"> list</li>\n</ul>\n</div>", $html);
    }

    public function testPostprocess(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJDYWxXSkxPUWZuXzB5ZUdmMlNvSHBBIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENPdWxKMmNOU2xIRm03S0FVaGtqTnoxYWJIdXgzR3VzIn0&quot;)" class="linkmail">
                    E-Mail
                </a>
                ZZZZZZZZZZ,
            $html_utils->postprocess('e.mail+test@other-domain.com')
        );
    }

    public function testReplacePureEmailAdresses(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJDYWxXSkxPUWZuXzB5ZUdmMlNvSHBBIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENPdWxKMmNOU2xIRm03S0FVaGtqTnoxYWJIdXgzR3VzIn0&quot;)" class="linkmail">
                    E-Mail
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('e.mail+test@other-domain.com')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJDYWxXSkxPUWZuXzB5ZUdmMlNvSHBBIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENPdWxKMmNOU2xIRm03S0FVaGtqTnoxYWJIdXgzR3VzIn0&quot;)" class="linkmail">
                    E-Mail
                </a>.
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: e.mail+test@other-domain.com.')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJDYWxXSkxPUWZuXzB5ZUdmMlNvSHBBIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENPdWxKMmNOU2xIRm03S0FVaGtqTnoxYWJIdXgzR3VzIn0&quot;)" class="linkmail">
                    E-Mail
                </a>, <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJ3VHZZbHE5R09JOWtJTmtmRDJJeUpnIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWTNPYkFibUV4a0xEMUNDYTEwRVpvLXZrVjZQQS1oSkhSbmE0MlphbUt4Y0pFLVlvbXRsTDFEY2hfS0ZVMWtQYjF4amMifQ&quot;)" class="linkmail">
                    E-Mail
                </a>.
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: e.mail+test@other-domain.com, e.mail@other-domain.com.')
        );
    }

    public function testReplaceMailToLinksWithoutSubject(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJMVmFicDM4eEZhd2hfWWFaZjRlMVNRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENLNkYzeklnU2xDNHhfYjhUajFtTWdSclVIUFZtIn0&quot;)" class="linkmail">
                    Test
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('<a href="mailto:e.mail+test@other-domain.com">Test</a>')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiIycGYxUHhSajE0ZXViczFWR2NlaFlRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENQS3Rxek10ZHduMHAtdmRhemw3YjJSN0VFLTA1VFBndjRtMzUifQ&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:e.mail+test@other-domain.com" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiIycGYxUHhSajE0ZXViczFWR2NlaFlRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENQS3Rxek10ZHduMHAtdmRhemw3YjJSN0VFLTA1VFBndjRtMzUifQ&quot;)" class="linkmail">
                    Contact me
                </a> <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJ2R195bFJaOWZFd2J1cERiRkhJdUVBIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWTNPYkFibUV4a0xEMUNDYTEwRVpvLXZrVjZQQS1oSkhSbmE0MlpEa0NBSUJITEFrMWM4Y21uODM2cmNjaVU3YW1VN1BCZlYzQ3cifQ&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:e.mail+test@other-domain.com" class="linkmail">Contact me</a> <a name="" href="mailto:e.mail@other-domain.com">Contact me</a>!')
        );
    }

    public function testReplaceMailToLinksWithSubject(): void {
        $html_utils = new HtmlUtils();
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJpeWtxRlB2eVp6VmQzYl9BQ1NyN0ZBIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENLNkYzeklnU2xDNHhfYjhUajFtTWdWYlZGZXB2Vk9zIn0&quot;)" class="linkmail">
                    Test
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('<a href="mailto:e.mail+test@other-domain.com?subject=test">Test</a>')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiI4enljekFXQWRfTHlhYnBNMW84RWZ3IiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENQS3Rxek10ZHduMHAtdmRhemw3YjJSN0VFLTA1VExRNzRHN3c2Vl9Ca19rMV9jeHVRNGpPIn0&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:e.mail+test@other-domain.com?subject=another%20test" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiI4enljekFXQWRfTHlhYnBNMW84RWZ3IiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWXhpQUVLS1Y5QURUMHlpSmswdFlyZVhnRzZHQjRSb2RIbmIybnF2X1JFeENQS3Rxek10ZHduMHAtdmRhemw3YjJSN0VFLTA1VExRNzRHN3c2Vl9Ca19rMV9jeHVRNGpPIn0&quot;)" class="linkmail">
                    Contact me
                </a>, <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJTQUtlZGxHQlhSRF8wckl1NENsTURRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmTWp1allNWTNPYkFibUV4a0xEMUNDYTEwRVpvLXZrVjZQQS1oSkhSbmE0MlpEa0NBSUJITEFrMWM4Y21uODM2cmNjaVU3YW1VNkRFZmQwQXY0X19DUzJzVTdXeGI4bjlBIn0&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:e.mail+test@other-domain.com?subject=another%20test" class="linkmail">Contact me</a>, <a name="" href="mailto:e.mail@other-domain.com?subject=another%20test">Contact me</a>!')
        );
    }

    public function testReplacePureOlzAdresses(): void {
        $html_utils = new HtmlUtils();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['user_email' => true];
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzRoleInfoModal(3)" class="linkrole">
                    Vorstand
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('vorstand-role@staging.olzimmerberg.ch')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJGUlNlOV90VVZGa3drWDlYZ3JOS2RRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzgzQUpfcndhemdHTXlBSERHdng0QXJSZzRIVG83VWMifQ&quot;)" class="linkmail">
                    E-Mail
                </a>.
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: inexistent@staging.olzimmerberg.ch.')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzRoleInfoModal(2)" class="linkrole">
                    Administrator
                </a>, <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJGUlNlOV90VVZGa3drWDlYZ3JOS2RRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzgzQUpfcndhemdHTXlBSERHdng0QXJSZzRIVG83VWMifQ&quot;)" class="linkmail">
                    E-Mail
                </a>.
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: admin_role@staging.olzimmerberg.ch, inexistent@staging.olzimmerberg.ch.')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJLS2U0NGRETks3bHlVMWNJNUlhOG5BIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFlQmlwU1FSYmwyUU5hS1YxUWpPMVNyVjBVTk5xZW5rRVAyQTZ3VllIRGZxMmYtcEVoTVlDLVktbXU4VC16d3Q4X2Rhemw3YjJSN0VFLTA1VFBndjRtMzUifQ&quot;)" class="linkmail">
                    E-Mail
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('vorstand@staging.olzimmerberg.ch')
        );
    }

    public function testReplacePureOlzAdressesWithoutPermission(): void {
        $html_utils = new HtmlUtils();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['user_email' => false];
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzRoleInfoModal(3)" class="linkrole">
                    Vorstand
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('vorstand-role@staging.olzimmerberg.ch')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJGUlNlOV90VVZGa3drWDlYZ3JOS2RRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzgzQUpfcndhemdHTXlBSERHdng0QXJSZzRIVG83VWMifQ&quot;)" class="linkmail">
                    E-Mail
                </a>.
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: inexistent@staging.olzimmerberg.ch.')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzRoleInfoModal(2)" class="linkrole">
                    Administrator
                </a>, <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJGUlNlOV90VVZGa3drWDlYZ3JOS2RRIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzgzQUpfcndhemdHTXlBSERHdng0QXJSZzRIVG83VWMifQ&quot;)" class="linkmail">
                    E-Mail
                </a>.
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: admin_role@staging.olzimmerberg.ch, inexistent@staging.olzimmerberg.ch.')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJLS2U0NGRETks3bHlVMWNJNUlhOG5BIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFlQmlwU1FSYmwyUU5hS1YxUWpPMVNyVjBVTk5xZW5rRVAyQTZ3VllIRGZxMmYtcEVoTVlDLVktbXU4VC16d3Q4X2Rhemw3YjJSN0VFLTA1VFBndjRtMzUifQ&quot;)" class="linkmail">
                    E-Mail
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('vorstand@staging.olzimmerberg.ch')
        );
    }

    public function testReplaceOlzMailToLinksWithoutSubject(): void {
        $html_utils = new HtmlUtils();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['user_email' => true];
        $html = $html_utils->replaceEmailAdresses('<a href="mailto:vorstand-role@staging.olzimmerberg.ch">Test</a>');
        $this->assertSame(<<<'ZZZZZZZZZZ'
            <a href="#" onclick="return olz.initOlzRoleInfoModal(3)" class="linkrole">
                Test
            </a>
            ZZZZZZZZZZ, $html);
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJack40TUNPWHNRWkMzMlhmcFdOQkpnIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzlUSXE2N1FWbUEzRDNsYU5VdXB1RlB3XzdYV211MVRHMnFkNCJ9&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:inexistent@staging.olzimmerberg.ch" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzRoleInfoModal(2)" class="linkrole">
                    Contact me
                </a> <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJack40TUNPWHNRWkMzMlhmcFdOQkpnIiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzlUSXE2N1FWbUEzRDNsYU5VdXB1RlB3XzdYV211MVRHMnFkNCJ9&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:admin_role@staging.olzimmerberg.ch" class="linkmail">Contact me</a> <a name="" href="mailto:inexistent@staging.olzimmerberg.ch">Contact me</a>!')
        );
    }

    public function testReplaceOlzMailToLinksWithSubject(): void {
        $html_utils = new HtmlUtils();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['user_email' => true];
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                <a href="#" onclick="return olz.initOlzRoleInfoModal(3)" class="linkrole">
                    <b>Bold</b> Test
                </a>
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('<a href="mailto:vorstand-role@staging.olzimmerberg.ch?subject=test"><b>Bold</b> Test</a>')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mail: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJMeFk3dlMzTXVrekZSQXEtWmJTNW13IiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzlUSXE2N1FWbUEzRDNsYU5VdXB1RlB3XzdYV211eGpTMktSeDRjeHZFcGlETlEyY2ZlS0kifQ&quot;)" class="linkmail">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mail: <a name="" href="mailto:inexistent@staging.olzimmerberg.ch?subject=another%20test" class="linkmail">Contact me</a>!')
        );
        $this->assertSame(
            <<<'ZZZZZZZZZZ'
                Mails: <a href="#" onclick="return olz.initOlzEmailModal(&quot;eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJMeFk3dlMzTXVrekZSQXEtWmJTNW13IiwiY2lwaGVydGV4dCI6IlVLMW00TlJxY2FOUWFmOWpzaThNZkVlUkc2V2h4eHZHM0NTVjJRRllyUDdnR09LSF9CVmFRRE9zbUx1cFNsUVVHcnh3bXBBYzlUSXE2N1FWbUEzRDNsYU5VdXB1RlB3XzdYV211eGpTMktSeDRjeHZFcGlETlEyY2ZlS0kifQ&quot;)" class="linkmail">
                    Contact me
                </a>, <a href="#" onclick="return olz.initOlzRoleInfoModal(2)" class="linkrole">
                    Contact me
                </a>!
                ZZZZZZZZZZ,
            $html_utils->replaceEmailAdresses('Mails: <a href="mailto:inexistent@staging.olzimmerberg.ch?subject=another%20test" class="linkmail">Contact me</a>, <a name="" href="mailto:admin_role@staging.olzimmerberg.ch?subject=another%20test">Contact me</a>!')
        );
        $this->assertSame([], $this->getLogs());
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
