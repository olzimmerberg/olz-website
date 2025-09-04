<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\EmailUtils;
use Olz\Utils\EmailUtilsTrait;

class EmailUtilsTraitConcreteUtils {
    use EmailUtilsTrait;

    public function testOnlyEmailUtils(): EmailUtils {
        return $this->emailUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\EmailUtilsTrait
 */
final class EmailUtilsTraitTest extends UnitTestCase {
    public function testSetGetEmailUtils(): void {
        $utils = new EmailUtilsTraitConcreteUtils();
        $fake = $this->createMock(EmailUtils::class);
        $utils->setEmailUtils($fake);
        $this->assertSame($fake, $utils->testOnlyEmailUtils());
    }
}
