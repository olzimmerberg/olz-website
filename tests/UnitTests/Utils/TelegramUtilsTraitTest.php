<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\TelegramUtils;
use Olz\Utils\TelegramUtilsTrait;

class TelegramUtilsTraitConcreteUtils {
    use TelegramUtilsTrait;

    public function testOnlyTelegramUtils(): TelegramUtils {
        return $this->telegramUtils();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\TelegramUtilsTrait
 */
final class TelegramUtilsTraitTest extends UnitTestCase {
    public function testSetGetTelegramUtils(): void {
        $utils = new TelegramUtilsTraitConcreteUtils();
        $fake = $this->createMock(TelegramUtils::class);
        $utils->setTelegramUtils($fake);
        $this->assertSame($fake, $utils->testOnlyTelegramUtils());
    }
}
