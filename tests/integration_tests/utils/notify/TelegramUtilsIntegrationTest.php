<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/notify/TelegramUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \TelegramUtils
 */
final class TelegramUtilsIntegrationTest extends IntegrationTestCase {
    public function testTelegramUtilsFromEnv(): void {
        $telegram_utils = getTelegramUtilsFromEnv();
        $this->assertSame('olz_bot', $telegram_utils->getBotName());
    }
}
