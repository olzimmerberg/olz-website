<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\MailerTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailerTraitConcreteUtils {
    use MailerTrait;

    public function testOnlyMailer(): MailerInterface {
        return $this->mailer();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\MailerTrait
 */
final class MailerTraitTest extends UnitTestCase {
    public function testSetGetMailer(): void {
        $utils = new MailerTraitConcreteUtils();
        $fake = $this->createMock(MailerInterface::class);
        $utils->setMailer($fake);
        $this->assertSame($fake, $utils->testOnlyMailer());
    }
}
