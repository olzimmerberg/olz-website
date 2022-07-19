<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\TelegramUtils;

/**
 * @internal
 * @covers \Olz\Utils\TelegramUtils
 */
final class TelegramUtilsIntegrationTest extends IntegrationTestCase {
    public function testTelegramUtilsFromEnv(): void {
        $telegram_utils = TelegramUtils::fromEnv();
        $this->assertSame('olz_bot', $telegram_utils->getBotName());
    }
}
