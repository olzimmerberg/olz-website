<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../../../../src/utils/notify/OlzMailer.php';

class FakeOlzMailerEmailUtils {
    public function encryptEmailReactionToken($data) {
        $general_utils = new GeneralUtils();
        return $general_utils->base64EncodeUrl(json_encode($data));
    }
}

class FakeOlzMailerServerConfig {
    public function getBaseHref() {
        return 'http://fake-base-url';
    }

    public function getCodeHref() {
        return '/_/';
    }
}

/**
 * @internal
 * @covers \OlzMailer
 */
final class OlzMailerTest extends TestCase {
    public function testConfigure(): void {
        $email_utils = new FakeOlzMailerEmailUtils();
        $server_config = new FakeOlzMailerServerConfig();
        $mailer = new OlzMailer($email_utils, $server_config, true);

        $user = new User();
        $user->setEmail('fake-user@olzimmerberg.ch');
        $user->setFirstName('Fake');
        $user->setLastName('User');
        $mailer->configure($user, 'Tèśt', "äsdf\n1234");

        $this->assertSame([
            ['fake-user@olzimmerberg.ch', 'Fake User'],
        ], $mailer->getToAddresses());
        $this->assertSame([], $mailer->getCcAddresses());
        $this->assertSame([], $mailer->getBccAddresses());
        $this->assertSame('text/html', $mailer->ContentType);
        $this->assertSame('[OLZ] Tèśt', $mailer->Subject);
        $expected_html = <<<ZZZZZZZZZZ
        <div style="text-align: right; float: right;">
            <img src="cid:olz_logo" alt="" style="width:150px;" />
        </div>
        Hallo <b>Fake</b>,<br />
        äsdf<br />\n1234<br />
        <br />
        <hr style="border: 0; border-top: 1px solid black;">
        Abmelden? <a href="http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOm51bGwsIm5vdGlmaWNhdGlvbl90eXBlIjoibW9udGhseV9wcmV2aWV3In0">Keine solchen E-Mails mehr</a> - <a href="http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJ1bnN1YnNjcmliZSIsInVzZXIiOm51bGwsIm5vdGlmaWNhdGlvbl90eXBlX2FsbCI6dHJ1ZX0">Keine E-Mails von OL Zimmerberg mehr</a>
        ZZZZZZZZZZ;
        $this->assertSame($expected_html, $mailer->Body);
        $expected_text = <<<ZZZZZZZZZZ
        Hallo Fake,

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
        $email_utils = new FakeOlzMailerEmailUtils();
        $server_config = new FakeOlzMailerServerConfig();
        $mailer = new OlzMailer($email_utils, $server_config, true);

        try {
            $mailer->send();
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('You must provide at least one recipient email address.', $exc->getMessage());
        }
    }

    public function testSendConfigured(): void {
        $email_utils = new FakeOlzMailerEmailUtils();
        $server_config = new FakeOlzMailerServerConfig();
        $mailer = new OlzMailer($email_utils, $server_config, true);
        $user = new User();
        $user->setEmail('fake-user@olzimmerberg.ch');
        $user->setFirstName('Fake');
        $user->setLastName('User');
        $mailer->configure($user, 'Tèśt', "äsdf\n1234");

        try {
            $mailer->send();
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('Invalid address:  (From): root@localhost', $exc->getMessage());
        }
    }
}
