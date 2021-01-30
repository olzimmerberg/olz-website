<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/notify/EmailUtils.php';

/**
 * @internal
 * @covers \EmailUtils
 */
final class EmailUtilsIntegrationTest extends TestCase {
    public function testEmailUtilsFromEnv(): void {
        global $_CONFIG;
        require_once __DIR__.'/../../../../src/config/server.php';

        $email_utils = getEmailUtilsFromEnv();

        $mailer = $email_utils->createEmail();

        $this->assertSame(null, $mailer->Priority);
        $this->assertSame('UTF-8', $mailer->CharSet);
        $this->assertSame('text/plain', $mailer->ContentType);
        $this->assertSame('base64', $mailer->Encoding);
        $this->assertSame('', $mailer->ErrorInfo);
        $this->assertSame($_CONFIG->getSmtpFrom(), $mailer->From);
        $this->assertSame('OL Zimmerberg', $mailer->FromName);
        $this->assertSame($_CONFIG->getSmtpFrom(), $mailer->Sender);
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
        $this->assertSame($_CONFIG->getSmtpHost(), $mailer->Host);
        $this->assertSame(intval($_CONFIG->getSmtpPort()), $mailer->Port);
        $this->assertSame('', $mailer->Helo);
        $this->assertSame('ssl', $mailer->SMTPSecure);
        $this->assertSame(true, $mailer->SMTPAutoTLS);
        $this->assertSame(true, $mailer->SMTPAuth);
        $this->assertSame([], $mailer->SMTPOptions);
        $this->assertSame($_CONFIG->getSmtpUsername(), $mailer->Username);
        $this->assertSame($_CONFIG->getSmtpPassword(), $mailer->Password);
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
