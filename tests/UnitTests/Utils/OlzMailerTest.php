<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Monolog\Logger;
use Olz\Entity\User;
use Olz\Tests\Fake\FakeEmailUtils;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\OlzMailer;

/**
 * @internal
 *
 * @coversNothing
 */
class OlzMailerForTest extends OlzMailer {
    public $waited_some_time = false;

    protected function waitSomeTime() {
        $this->waited_some_time = true;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\OlzMailer
 */
final class OlzMailerTest extends UnitTestCase {
    public function testConfigure(): void {
        $email_utils = new FakeEmailUtils();
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OlzMailerTest');
        $mailer = new OlzMailerForTest($email_utils, $server_config, true);
        $mailer->setLogger($logger);

        $user = new User();
        $user->setEmail('fake-user@olzimmerberg.ch');
        $user->setFirstName('Fake');
        $user->setLastName('User');
        $mailer->configure($user, 'Tèśt', "äsdf\n1234", ['notification_type' => 'monthly_preview']);

        $this->assertSame([
            ['fake-user@olzimmerberg.ch', 'Fake User'],
        ], $mailer->getToAddresses());
        $this->assertSame([], $mailer->getCcAddresses());
        $this->assertSame([], $mailer->getBccAddresses());
        $this->assertSame('text/html', $mailer->ContentType);
        $this->assertSame('Tèśt', $mailer->Subject);
        $expected_html = <<<ZZZZZZZZZZ
        <div style="text-align: right; float: right;">
            <img src="cid:olz_logo" alt="" style="width:150px;" />
        </div>
        <br /><br /><br />
        äsdf\n1234
        <br /><br />
        <hr style="border: 0; border-top: 1px solid black;">
        Abmelden? <a href="http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOm51bGwsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0">Keine solchen E-Mails mehr</a> oder <a href="http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOm51bGwsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>
        ZZZZZZZZZZ;
        $this->assertSame($expected_html, $mailer->Body);
        $expected_text = <<<ZZZZZZZZZZ
        äsdf\n1234

        ---
        Abmelden?
        Keine solchen E-Mails mehr: http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOm51bGwsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0
        Keine E-Mails von OL Zimmerberg mehr: http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOm51bGwsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0
        ZZZZZZZZZZ;
        $this->assertSame($expected_text, $mailer->AltBody);
        $this->assertSame(1, count($mailer->getAttachments()));
        $first_attachment = $mailer->getAttachments()[0];
        $this->assertTrue(is_file($first_attachment[0]));
        $this->assertSame('inline', $first_attachment[6]);
        $this->assertSame('olz_logo', $first_attachment[7]);
    }

    public function testSend(): void {
        $email_utils = new FakeEmailUtils();
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OlzMailerTest');
        $mailer = new OlzMailerForTest($email_utils, $server_config, true);
        $mailer->setLogger($logger);

        try {
            $mailer->send();
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('You must provide at least one recipient email address.', $exc->getMessage());
            $this->assertSame(true, $mailer->waited_some_time);
        }
    }
}
