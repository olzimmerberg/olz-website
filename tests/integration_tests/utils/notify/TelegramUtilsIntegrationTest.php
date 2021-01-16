<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/notify/TelegramUtils.php';

/**
 * @internal
 * @covers \TelegramUtils
 */
final class TelegramUtilsIntegrationTest extends TestCase {
    public function testTelegramUtilsFromEnv(): void {
        global $_SERVER;
        $telegram_utils = getTelegramUtilsFromEnv();
        $this->assertSame('olz_bot', $telegram_utils->getBotName());
    }
}
