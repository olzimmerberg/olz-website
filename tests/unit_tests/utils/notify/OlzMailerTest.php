<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/model/User.php';
require_once __DIR__.'/../../../../src/utils/notify/OlzMailer.php';

/**
 * @internal
 * @covers \OlzMailer
 */
final class OlzMailerTest extends TestCase {
    public function testConfigure(): void {
        $mailer = new OlzMailer(true);

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
        $this->assertSame("HTML-<b>Test</b>,<br />\näsdf<br />\n1234", $mailer->Body);
        $this->assertSame("äsdf\n1234", $mailer->AltBody);
    }

    public function testSend(): void {
        $mailer = new OlzMailer(true);

        try {
            $mailer->send();
            $this->fail('Error expected');
        } catch (Exception $exc) {
            $this->assertSame('You must provide at least one recipient email address.', $exc->getMessage());
        }
    }

    public function testSendConfigured(): void {
        $mailer = new OlzMailer(true);
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
