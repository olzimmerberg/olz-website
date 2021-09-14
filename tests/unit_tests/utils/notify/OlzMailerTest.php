<?php

declare(strict_types=1);

use Monolog\Logger;

require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../../../../src/utils/notify/OlzMailer.php';
require_once __DIR__.'/../../../fake/FakeEmailUtils.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \OlzMailer
 */
final class OlzMailerTest extends UnitTestCase {
    public function testConfigure(): void {
        $email_utils = new FakeEmailUtils();
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OlzMailerTest');
        $mailer = new OlzMailer($email_utils, $server_config, true);
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
        $mailer = new OlzMailer($email_utils, $server_config, true);
        $mailer->setLogger($logger);

        try {
            $mailer->send();
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('You must provide at least one recipient email address.', $exc->getMessage());
        }
    }

    public function testSendConfigured(): void {
        $email_utils = new FakeEmailUtils();
        $server_config = new FakeEnvUtils();
        $logger = new Logger('OlzMailerTest');
        $mailer = new OlzMailer($email_utils, $server_config, true);
        $mailer->setLogger($logger);
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
