<?php

declare(strict_types=1);

use Monolog\Logger;
use PhpImap\Mailbox;

require_once __DIR__.'/../../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../../_/model/User.php';
require_once __DIR__.'/../../../../_/utils/GeneralUtils.php';
require_once __DIR__.'/../../../../_/utils/notify/EmailUtils.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeEnvUtilsForSendmail extends FakeEnvUtils {
    public function getSmtpHost() {
        return null;
    }
}

/**
 * @internal
 * @covers \EmailUtils
 */
final class EmailUtilsTest extends UnitTestCase {
    public function testGetImapMailbox(): void {
        $env_utils = new FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $logger = new Logger('EmailUtilsTest');
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);
        $email_utils->setGeneralUtils($general_utils);
        $email_utils->setLogger($logger);

        $mailbox = $email_utils->getImapMailbox();

        $this->assertSame(true, $mailbox instanceof Mailbox);
        $this->assertSame('{127.0.0.1:143/notls}INBOX', $mailbox->getImapPath());
        $this->assertSame('imap@olzimmerberg.ch', $mailbox->getLogin());
    }

    public function testEmailReactionToken(): void {
        $env_utils = new FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $logger = new Logger('EmailUtilsTest');
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);
        $email_utils->setGeneralUtils($general_utils);
        $email_utils->setLogger($logger);

        $token = $email_utils->encryptEmailReactionToken(['test' => 'data']);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\\-_]+$/', $token);
        $this->assertSame(
            ['test' => 'data'],
            $email_utils->decryptEmailReactionToken($token)
        );
    }

    public function testDecryptInvalidEmailReactionToken(): void {
        $env_utils = new FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $logger = new Logger('EmailUtilsTest');
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);
        $email_utils->setGeneralUtils($general_utils);
        $email_utils->setLogger($logger);

        $this->assertSame(null, $email_utils->decryptEmailReactionToken(''));
    }

    public function testCreateSendmailEmail(): void {
        $env_utils = new FakeEnvUtilsForSendmail();
        $general_utils = new GeneralUtils();
        $logger = new Logger('EmailUtilsTest');
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);
        $email_utils->setGeneralUtils($general_utils);
        $email_utils->setLogger($logger);

        $mailer = $email_utils->createEmail();

        $this->assertSame(null, $mailer->Priority);
        $this->assertSame('UTF-8', $mailer->CharSet);
        $this->assertSame('text/plain', $mailer->ContentType);
        $this->assertSame('base64', $mailer->Encoding);
        $this->assertSame('', $mailer->ErrorInfo);
        $this->assertSame('fake@olzimmerberg.ch', $mailer->From);
        $this->assertSame('OL Zimmerberg', $mailer->FromName);
        $this->assertSame('fake@olzimmerberg.ch', $mailer->Sender);
        $this->assertSame('', $mailer->Subject);
        $this->assertSame('', $mailer->Body);
        $this->assertSame('', $mailer->AltBody);
        $this->assertSame('', $mailer->Ical);
        $this->assertSame(0, $mailer->WordWrap);
        $this->assertSame('sendmail', $mailer->Mailer);
        $this->assertSame(true, $mailer->UseSendmailOptions);
        $this->assertSame('', $mailer->ConfirmReadingTo);
        $this->assertSame('', $mailer->Hostname);
        $this->assertSame('', $mailer->MessageID);
        $this->assertSame('', $mailer->MessageDate);
        $this->assertSame('localhost', $mailer->Host);
        $this->assertSame(25, $mailer->Port);
        $this->assertSame('', $mailer->Helo);
        $this->assertSame('', $mailer->SMTPSecure);
        $this->assertSame(true, $mailer->SMTPAutoTLS);
        $this->assertSame(false, $mailer->SMTPAuth);
        $this->assertSame([], $mailer->SMTPOptions);
        $this->assertSame('', $mailer->Username);
        $this->assertSame('', $mailer->Password);
        $this->assertSame('', $mailer->AuthType);
        $this->assertSame(300, $mailer->Timeout);
        $this->assertSame('', $mailer->dsn);
        $this->assertSame(0, $mailer->SMTPDebug);
        $this->assertSame('echo', $mailer->Debugoutput);
        $this->assertSame(false, $mailer->SMTPKeepAlive);
        $this->assertSame(false, $mailer->SingleTo);
        $this->assertSame(false, $mailer->do_verp);
        $this->assertSame(false, $mailer->AllowEmpty);
        $this->assertSame('', $mailer->DKIM_selector);
        $this->assertSame('', $mailer->DKIM_identity);
        $this->assertSame('', $mailer->DKIM_passphrase);
        $this->assertSame('', $mailer->DKIM_domain);
        $this->assertSame(true, $mailer->DKIM_copyHeaderFields);
        $this->assertSame([], $mailer->DKIM_extraHeaders);
        $this->assertSame('', $mailer->DKIM_private);
        $this->assertSame('', $mailer->DKIM_private_string);
        $this->assertSame('', $mailer->action_function);
        $this->assertSame('', $mailer->XMailer);
    }

    public function testCreateSmtpEmail(): void {
        $env_utils = new FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $logger = new Logger('EmailUtilsTest');
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);
        $email_utils->setGeneralUtils($general_utils);
        $email_utils->setLogger($logger);

        $mailer = $email_utils->createEmail();

        $this->assertSame(null, $mailer->Priority);
        $this->assertSame('UTF-8', $mailer->CharSet);
        $this->assertSame('text/plain', $mailer->ContentType);
        $this->assertSame('base64', $mailer->Encoding);
        $this->assertSame('', $mailer->ErrorInfo);
        $this->assertSame('fake@olzimmerberg.ch', $mailer->From);
        $this->assertSame('OL Zimmerberg', $mailer->FromName);
        $this->assertSame('fake@olzimmerberg.ch', $mailer->Sender);
        $this->assertSame('', $mailer->Subject);
        $this->assertSame('', $mailer->Body);
        $this->assertSame('', $mailer->AltBody);
        $this->assertSame('', $mailer->Ical);
        $this->assertSame(0, $mailer->WordWrap);
        $this->assertSame('smtp', $mailer->Mailer);
        $this->assertSame(true, $mailer->UseSendmailOptions);
        $this->assertSame('', $mailer->ConfirmReadingTo);
        $this->assertSame('', $mailer->Hostname);
        $this->assertSame('', $mailer->MessageID);
        $this->assertSame('', $mailer->MessageDate);
        $this->assertSame('localhost', $mailer->Host);
        $this->assertSame(25, $mailer->Port);
        $this->assertSame('', $mailer->Helo);
        $this->assertSame('ssl', $mailer->SMTPSecure);
        $this->assertSame(true, $mailer->SMTPAutoTLS);
        $this->assertSame(true, $mailer->SMTPAuth);
        $this->assertSame([], $mailer->SMTPOptions);
        $this->assertSame('fake@olzimmerberg.ch', $mailer->Username);
        $this->assertSame('1234', $mailer->Password);
        $this->assertSame('', $mailer->AuthType);
        $this->assertSame(300, $mailer->Timeout);
        $this->assertSame('', $mailer->dsn);
        $this->assertSame(0, $mailer->SMTPDebug);
        $this->assertSame('echo', $mailer->Debugoutput);
        $this->assertSame(false, $mailer->SMTPKeepAlive);
        $this->assertSame(false, $mailer->SingleTo);
        $this->assertSame(false, $mailer->do_verp);
        $this->assertSame(false, $mailer->AllowEmpty);
        $this->assertSame('', $mailer->DKIM_selector);
        $this->assertSame('', $mailer->DKIM_identity);
        $this->assertSame('', $mailer->DKIM_passphrase);
        $this->assertSame('', $mailer->DKIM_domain);
        $this->assertSame(true, $mailer->DKIM_copyHeaderFields);
        $this->assertSame([], $mailer->DKIM_extraHeaders);
        $this->assertSame('', $mailer->DKIM_private);
        $this->assertSame('', $mailer->DKIM_private_string);
        $this->assertSame('', $mailer->action_function);
        $this->assertSame('', $mailer->XMailer);
    }

    public function testRenderMarkdown(): void {
        $env_utils = new FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $logger = new Logger('EmailUtilsTest');
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);
        $email_utils->setGeneralUtils($general_utils);
        $email_utils->setLogger($logger);

        // Ignore HTML
        $html = $email_utils->renderMarkdown("Normal<h1>H1</h1><script>alert('not good!');</script>");
        $this->assertSame("<p>NormalH1alert('not good!');</p>\n", $html);

        // Headings
        $html = $email_utils->renderMarkdown("Normal\n# H1\n## H2\n### H3\nNormal");
        $this->assertSame("<p>Normal</p>\n<h1>H1</h1>\n<h2>H2</h2>\n<h3>H3</h3>\n<p>Normal</p>\n", $html);

        // Font style
        $html = $email_utils->renderMarkdown("Normal **fe\\*\\*tt** __fe\\_\\_tt__");
        $this->assertSame("<p>Normal <strong>fe**tt</strong> <strong>fe__tt</strong></p>\n", $html);
        $html = $email_utils->renderMarkdown("Normal *kur\\*siv* _kur\\_siv_");
        $this->assertSame("<p>Normal <em>kur*siv</em> <em>kur_siv</em></p>\n", $html);
        $html = $email_utils->renderMarkdown("Normal ~~durch\\~\\~gestrichen~~");
        $this->assertSame("<p>Normal <del>durch~~gestrichen</del></p>\n", $html);

        // Quotes
        $html = $email_utils->renderMarkdown("Normal\n> quote\nstill quote\n\nnot anymore");
        $this->assertSame("<p>Normal</p>\n<blockquote>\n<p>quote\nstill quote</p>\n</blockquote>\n<p>not anymore</p>\n", $html);

        // Ordered lists
        $html = $email_utils->renderMarkdown("Normal\n1. one\n2. two\n3. three\nstill three\n\nnot anymore");
        $this->assertSame("<p>Normal</p>\n<ol>\n<li>one</li>\n<li>two</li>\n<li>three\nstill three</li>\n</ol>\n<p>not anymore</p>\n", $html);

        // Unordered lists
        $html = $email_utils->renderMarkdown("Normal\n- one\n- two\n- three\nstill three\n\nnot anymore");
        $this->assertSame("<p>Normal</p>\n<ul>\n<li>one</li>\n<li>two</li>\n<li>three\nstill three</li>\n</ul>\n<p>not anymore</p>\n", $html);

        // Code
        $html = $email_utils->renderMarkdown("Normal `co\\`de`");
        $this->assertSame("<p>Normal <code>co\\</code>de`</p>\n", $html);
        $html = $email_utils->renderMarkdown("Normal ```co`de```");
        $this->assertSame("<p>Normal <code>co`de</code></p>\n", $html);
        $html = $email_utils->renderMarkdown("Normal\n```python\nco`de\n```");
        $this->assertSame("<p>Normal</p>\n<pre><code class=\"language-python\">co`de\n</code></pre>\n", $html);

        // Horizontal rule
        $html = $email_utils->renderMarkdown("something\n\n---\n\ndifferent");
        $this->assertSame("<p>something</p>\n<hr />\n<p>different</p>\n", $html);

        // Links
        $html = $email_utils->renderMarkdown("Normal [link](http://127.0.0.1/)");
        $this->assertSame("<p>Normal <a href=\"http://127.0.0.1/\">link</a></p>\n", $html);
        $html = $email_utils->renderMarkdown("Normal http://127.0.0.1/");
        $this->assertSame("<p>Normal <a href=\"http://127.0.0.1/\">http://127.0.0.1/</a></p>\n", $html);

        // Image
        $html = $email_utils->renderMarkdown("Normal ![bird](img/bird.jpg)");
        $this->assertSame("<p>Normal <img src=\"img/bird.jpg\" alt=\"bird\" /></p>\n", $html);

        // Table
        $html = $email_utils->renderMarkdown("Normal\n\n| left | middle | right |\n| --- | --- | --- |\n| 1 | 2 | 3 |\n\nafter");
        $this->assertSame("<p>Normal</p>\n<table>\n<thead>\n<tr>\n<th>left</th>\n<th>middle</th>\n<th>right</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td>1</td>\n<td>2</td>\n<td>3</td>\n</tr>\n</tbody>\n</table>\n<p>after</p>\n", $html);

        // Footnote
        $html = $email_utils->renderMarkdown("This. [^1]\n\n[^1]: explains everything\n");
        // does not work
        $this->assertSame("<p>This. [^1]</p>\n<p>[^1]: explains everything</p>\n", $html);

        // Heading ID
        $html = $email_utils->renderMarkdown("# So linkable {#anchor}\n");
        // does not work
        $this->assertSame("<h1>So linkable {#anchor}</h1>\n", $html);

        // Heading ID
        $html = $email_utils->renderMarkdown("- [x] finish\n- [ ] this\n- [ ] list\n");
        $this->assertSame("<ul>\n<li><input checked=\"\" disabled=\"\" type=\"checkbox\"> finish</li>\n<li><input disabled=\"\" type=\"checkbox\"> this</li>\n<li><input disabled=\"\" type=\"checkbox\"> list</li>\n</ul>\n", $html);
    }
}
