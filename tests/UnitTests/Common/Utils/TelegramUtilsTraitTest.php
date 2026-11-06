<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\TelegramUtils;
use Olz\Common\Utils\TelegramUtilsTrait;
use Olz\Tests\UnitTests\Common\UnitTestCase;

class TelegramUtilsTraitConcreteUtils {
    use TelegramUtilsTrait;

    public function testOnlyTelegramUtils(): TelegramUtils {
        return $this->telegramUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\TelegramUtilsTrait
 */
final class TelegramUtilsTraitTest extends UnitTestCase {
    public function testSetGetTelegramUtils(): void {
        $utils = new TelegramUtilsTraitConcreteUtils();
        $fake = $this->createMock(TelegramUtils::class);
        $utils->setTelegramUtils($fake);
        $this->assertSame($fake, $utils->testOnlyTelegramUtils());
    }
}
