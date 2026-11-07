<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\EmailUtils;
use Olz\Common\Utils\EmailUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class EmailUtilsTraitConcreteUtils {
    use EmailUtilsTrait;

    public function testOnlyEmailUtils(): EmailUtils {
        return $this->emailUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\EmailUtilsTrait
 */
final class EmailUtilsTraitTest extends UnitTestCase {
    public function testSetGetEmailUtils(): void {
        $utils = new EmailUtilsTraitConcreteUtils();
        $fake = $this->createMock(EmailUtils::class);
        $utils->setEmailUtils($fake);
        $this->assertSame($fake, $utils->testOnlyEmailUtils());
    }
}
