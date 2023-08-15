<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Entity\User;
use Olz\Exceptions\RecaptchaDeniedException;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\EmailUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Webklex\PHPIMAP\Client;

class FakeEnvUtilsForSendmail extends Fake\FakeEnvUtils {
    public function getSmtpHost() {
        return null;
    }
}

class TestOnlyEmailUtils extends EmailUtils {
    public function testOnlyGetRandomEmailVerificationToken() {
        return $this->getRandomEmailVerificationToken();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\EmailUtils
 */
final class EmailUtilsTest extends UnitTestCase {
    public function testSendEmailVerificationEmail(): void {
        $user = Fake\FakeUsers::defaultUser();
        $mailer = $this->createPartialMock(MailerInterface::class, ['send']);
        $email_utils = new Fake\DeterministicEmailUtils();
        $email_utils->setMailer($mailer);
        $email_utils->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $email_utils->sendEmailVerificationEmail($user, 'valid-recaptcha');

        $this->assertSame([
            "INFO Email verification email sent to user (1).",
        ], $this->getLogs());
        $expected_email = <<<'ZZZZZZZZZZ'
        **!!! Falls du nicht soeben auf olzimmerberg.ch deine E-Mail-Adresse bestätigen wolltest, lösche diese E-Mail !!!**

        Hallo Default,

        *Um deine E-Mail-Adresse zu bestätigen*, klicke [hier](http://fake-base-url/_/email_reaktion.php?token=eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiIzSDhXWDdxQWtlUU16R1U0c1ZmZlJBIiwiY2lwaGVydGV4dCI6IlVHOHNfbV9PVXFWX0tQSEVhZkNhbkZVc094dEkwbkdla0dOUFVfZ0ZLTlVmc2ZvMDdRdk10Ri1MUGZGbDMwR0h2UTRVSmFXVktkY01ZcHJGLTdWQ2g3Z1dJOGlZdnNCbGU4SFd2OTk5aEZOSkRZdnc4b19WWDMwM1hhR0kxZW8tc1hWcEFSTF8ifQ) oder auf folgenden Link:

        http://fake-base-url/_/email_reaktion.php?token=eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiIzSDhXWDdxQWtlUU16R1U0c1ZmZlJBIiwiY2lwaGVydGV4dCI6IlVHOHNfbV9PVXFWX0tQSEVhZkNhbkZVc094dEkwbkdla0dOUFVfZ0ZLTlVmc2ZvMDdRdk10Ri1MUGZGbDMwR0h2UTRVSmFXVktkY01ZcHJGLTdWQ2g3Z1dJOGlZdnNCbGU4SFd2OTk5aEZOSkRZdnc4b19WWDMwM1hhR0kxZW8tc1hWcEFSTF8ifQ

        ZZZZZZZZZZ;
        $this->assertSame([
            <<<ZZZZZZZZZZ
            From: 
            Reply-To: 
            To: "Default User" <default-user@staging.olzimmerberg.ch>
            Cc: 
            Bcc: 
            Subject: [OLZ] E-Mail bestätigen

            {$expected_email}


            <div style="text-align: right; float: right;">
                <img src="cid:olz_logo" alt="" style="width:150px;" />
            </div>
            <br /><br /><br />
            {$expected_email}


            olz_logo
            ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testSendEmailVerificationEmailInvalidRecaptcha(): void {
        $user = Fake\FakeUsers::defaultUser();
        $email_utils = new Fake\DeterministicEmailUtils();
        $email_utils->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        try {
            $email_utils->sendEmailVerificationEmail($user, 'invalid-recaptcha');
            $this->fail('Error expected');
        } catch (RecaptchaDeniedException $exc) {
            $this->assertSame([
                "WARNING reCaptcha token was invalid",
            ], $this->getLogs());
            $this->assertSame(
                'ReCaptcha Token ist ungültig',
                $exc->getMessage()
            );
        } catch (\Throwable $th) {
            $this->fail('RecaptchaDeniedException expected');
        }
    }

    public function testSendEmailVerificationEmailFailsSending(): void {
        $user = Fake\FakeUsers::defaultUser();
        $mailer = $this->createStub(MailerInterface::class);
        $email_utils = new Fake\DeterministicEmailUtils();
        $email_utils->setMailer($mailer);
        $email_utils->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $mailer
            ->expects($this->exactly(1))
            ->method('send')
            ->will($this->throwException(new \Exception('mocked-error')))
        ;

        try {
            $email_utils->sendEmailVerificationEmail($user, 'valid-recaptcha');
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame([
                "CRITICAL Error sending email verification email to user (1): mocked-error",
            ], $this->getLogs());
            $this->assertSame(
                'Error sending email verification email to user (1): mocked-error',
                $exc->getMessage()
            );
        }
    }

    public function testGetImapClient(): void {
        $email_utils = new EmailUtils();

        $client = $email_utils->getImapClient();

        $this->assertSame(true, $client instanceof Client);
        $this->assertSame('127.0.0.1', $client->host);
        $this->assertSame(143, $client->port);
        $this->assertSame('imap@staging.olzimmerberg.ch', $client->username);
        $this->assertSame('123456', $client->password);
    }

    public function testEmailReactionToken(): void {
        $email_utils = new EmailUtils();

        $token = $email_utils->encryptEmailReactionToken(['test' => 'data']);

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\\-_]+$/', $token);
        $this->assertSame(
            ['test' => 'data'],
            $email_utils->decryptEmailReactionToken($token)
        );
    }

    public function testDecryptInvalidEmailReactionToken(): void {
        $email_utils = new EmailUtils();

        $this->assertSame(null, $email_utils->decryptEmailReactionToken(''));
    }

    public function testCreateSendmailEmail(): void {
        $env_utils = new FakeEnvUtilsForSendmail();
        $email_utils = new EmailUtils();
        $email_utils->setEnvUtils($env_utils);

        $mailer = $email_utils->createEmail();

        $this->assertSame(null, $mailer->Priority);
        $this->assertSame('UTF-8', $mailer->CharSet);
        $this->assertSame('text/plain', $mailer->ContentType);
        $this->assertSame('base64', $mailer->Encoding);
        $this->assertSame('', $mailer->ErrorInfo);
        $this->assertSame('fake@staging.olzimmerberg.ch', $mailer->From);
        $this->assertSame('OL Zimmerberg', $mailer->FromName);
        $this->assertSame('fake@staging.olzimmerberg.ch', $mailer->Sender);
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
        $email_utils = new EmailUtils();

        $mailer = $email_utils->createEmail();

        $this->assertSame(null, $mailer->Priority);
        $this->assertSame('UTF-8', $mailer->CharSet);
        $this->assertSame('text/plain', $mailer->ContentType);
        $this->assertSame('base64', $mailer->Encoding);
        $this->assertSame('', $mailer->ErrorInfo);
        $this->assertSame('fake@staging.olzimmerberg.ch', $mailer->From);
        $this->assertSame('OL Zimmerberg', $mailer->FromName);
        $this->assertSame('fake@staging.olzimmerberg.ch', $mailer->Sender);
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
        $this->assertSame('tls', $mailer->SMTPSecure);
        $this->assertSame(true, $mailer->SMTPAutoTLS);
        $this->assertSame(true, $mailer->SMTPAuth);
        $this->assertSame([], $mailer->SMTPOptions);
        $this->assertSame('fake@staging.olzimmerberg.ch', $mailer->Username);
        $this->assertSame('1234', $mailer->Password);
        $this->assertSame('', $mailer->AuthType);
        $this->assertSame(300, $mailer->Timeout);
        $this->assertSame('', $mailer->dsn);
        $this->assertSame(3, $mailer->SMTPDebug);
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

    public function testBuildOlzEmail(): void {
        $email_utils = new EmailUtils();

        $user = new User();
        $user->setEmail('fake-user@staging.olzimmerberg.ch');
        $user->setFirstName('Fake');
        $user->setLastName('User');
        $email = new Email();

        $email = $email_utils->buildOlzEmail($email, $user, 'Tèśt', [
            'notification_type' => 'monthly_preview',
        ]);

        $this->assertSame([
        ], $this->getLogs());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            From: 
            Reply-To: 
            To: "Fake User" <fake-user@staging.olzimmerberg.ch>
            Cc: 
            Bcc: 
            Subject: 

            Tèśt

            ---
            Abmelden?
            Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion.php?token=eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJJemJPZlNkX01pcER3X29mNG13WFZ3IiwiY2lwaGVydGV4dCI6IlVHOHNfbV9PVXFWX0tQSEhZdkdHbUY4UUxCOUwzai1RbmpSSlJlOVZNSUZiOGJSOW9nVEtyQlRYZHZaaHpVbWR2eVZOS2FhVmVhMWRlNVRNNXJOSjBJZ0tLOFNEdXNCZ085ayJ9
            Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion.php?token=eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiIwTUROYWljSElnQUt0ZzZURDk2QlVnIiwiY2lwaGVydGV4dCI6IlVHOHNfbV9PVXFWX0tQSEhZdkdHbUY4UUxCOUwzai1RbmpSSlJlOVZNSUZiOGJSOW9nVEtyQlRYZHZaaHpVbWR2eVZOS2FhVkJQWVRldG1ZNXFsUXpLbyJ9

            <div style="text-align: right; float: right;">
                <img src="cid:olz_logo" alt="" style="width:150px;" />
            </div>
            <br /><br /><br />
            <p>Tèśt</p>

            <br /><br />
            <hr style="border: 0; border-top: 1px solid black;">
            Abmelden? <a href="http://fake-base-url/_/email_reaktion.php?token=eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiJJemJPZlNkX01pcER3X29mNG13WFZ3IiwiY2lwaGVydGV4dCI6IlVHOHNfbV9PVXFWX0tQSEhZdkdHbUY4UUxCOUwzai1RbmpSSlJlOVZNSUZiOGJSOW9nVEtyQlRYZHZaaHpVbWR2eVZOS2FhVmVhMWRlNVRNNXJOSjBJZ0tLOFNEdXNCZ085ayJ9">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion.php?token=eyJhbGdvIjoiYWVzLTI1Ni1nY20iLCJpdiI6IlFVRkJRVUZCUVVGQlFVRkIiLCJ0YWciOiIwTUROYWljSElnQUt0ZzZURDk2QlVnIiwiY2lwaGVydGV4dCI6IlVHOHNfbV9PVXFWX0tQSEhZdkdHbUY4UUxCOUwzai1RbmpSSlJlOVZNSUZiOGJSOW9nVEtyQlRYZHZaaHpVbWR2eVZOS2FhVkJQWVRldG1ZNXFsUXpLbyJ9">Keine E-Mails von OL Zimmerberg mehr</a>

            olz_logo
            ZZZZZZZZZZ, $email_utils->getComparableEmail($email));
    }

    public function testGetUserAddressWithoutName(): void {
        $email_utils = new EmailUtils();
        $user = new User();
        $user->setEmail('fake@staging.olzimmerberg.ch');
        $this->assertSame(
            'fake@staging.olzimmerberg.ch',
            $email_utils->getUserAddress($user)->toString(),
        );
    }

    public function testGetUserAddressWithName(): void {
        $email_utils = new EmailUtils();
        $user = new User();
        $user->setEmail('fake@staging.olzimmerberg.ch');
        $user->setFirstName('First');
        $user->setLastName('Last');
        $this->assertSame(
            '"First Last" <fake@staging.olzimmerberg.ch>',
            $email_utils->getUserAddress($user)->toString(),
        );
    }

    public function testRenderMarkdown(): void {
        $email_utils = new EmailUtils();

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

    public function testGetRandomEmailVerificationToken(): void {
        $email_utils = new TestOnlyEmailUtils();
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9_-]{8}$/',
            $email_utils->testOnlyGetRandomEmailVerificationToken()
        );
    }
}
