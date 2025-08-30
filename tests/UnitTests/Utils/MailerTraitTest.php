<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MailerTrait;
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
 * @covers \Olz\Utils\MailerTrait
 */
final class MailerTraitTest extends UnitTestCase {
    public function testSetGetMailer(): void {
        $utils = new MailerTraitConcreteUtils();
        $fake = $this->createMock(MailerInterface::class);
        $utils->setMailer($fake);
        $this->assertSame($fake, $utils->testOnlyMailer());
    }
}
