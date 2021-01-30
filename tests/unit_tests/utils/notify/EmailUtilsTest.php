<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/utils/notify/EmailUtils.php';

class FakeEmailUtilsServerConfig {
    public function getSmtpHost() {
        return 'localhost';
    }

    public function getSmtpPort() {
        return '25';
    }

    public function getSmtpUsername() {
        return 'fake@olzimmerberg.ch';
    }

    public function getSmtpPassword() {
        return '1234';
    }

    public function getSmtpFrom() {
        return 'fake@olzimmerberg.ch';
    }
}

/**
 * @internal
 * @covers \EmailUtils
 */
final class EmailUtilsTest extends TestCase {
    public function testCreateEmail(): void {
        $server_config = new FakeEmailUtilsServerConfig();
        $email_utils = new EmailUtils($server_config);

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
        $this->assertSame('/usr/sbin/sendmail', $mailer->Sendmail);
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
        $this->assertSame(2, $mailer->SMTPDebug);
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
}
