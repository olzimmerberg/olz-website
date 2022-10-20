<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EmailUtils;
use Olz\Utils\EnvUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\EmailUtils
 */
final class EmailUtilsIntegrationTest extends IntegrationTestCase {
    public function testEmailUtilsFromEnv(): void {
        $env_utils = EnvUtils::fromEnv();
        $email_utils = EmailUtils::fromEnv();

        $mailer = $email_utils->createEmail();

        $this->assertSame(null, $mailer->Priority);
        $this->assertSame('UTF-8', $mailer->CharSet);
        $this->assertSame('text/plain', $mailer->ContentType);
        $this->assertSame('base64', $mailer->Encoding);
        $this->assertSame('', $mailer->ErrorInfo);
        $this->assertSame($env_utils->getSmtpFrom(), $mailer->From);
        $this->assertSame('OL Zimmerberg', $mailer->FromName);
        $this->assertSame($env_utils->getSmtpFrom(), $mailer->Sender);
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
        $this->assertSame($env_utils->getSmtpHost(), $mailer->Host);
        $this->assertSame(intval($env_utils->getSmtpPort()), $mailer->Port);
        $this->assertSame('', $mailer->Helo);
        $this->assertSame('ssl', $mailer->SMTPSecure);
        $this->assertSame(true, $mailer->SMTPAutoTLS);
        $this->assertSame(true, $mailer->SMTPAuth);
        $this->assertSame([], $mailer->SMTPOptions);
        $this->assertSame($env_utils->getSmtpUsername(), $mailer->Username);
        $this->assertSame($env_utils->getSmtpPassword(), $mailer->Password);
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
}
